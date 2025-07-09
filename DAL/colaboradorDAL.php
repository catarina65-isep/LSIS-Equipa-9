<?php
require_once __DIR__ . '/Database.php';

class ColaboradorDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function criar($colaborador) {
        $sql = "INSERT INTO Colaborador (...) VALUES (...)";
        $stmt = $this->db->prepare($sql);
        // ... implementar bind de parâmetros
        return $stmt->execute();
    }

    public function buscarPorId($id_utilizador) {
        $sql = "SELECT * FROM Colaborador WHERE id_utilizador = :id_utilizador";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_utilizador' => $id_utilizador]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($dados) {
        // Primeiro, buscar os dados atuais do colaborador
        $colaboradorAtual = $this->buscarPorId($dados['id_utilizador']);
        
        // Preparar os campos para atualização
        $campos = [];
        $valores = [
            ':id_utilizador' => $dados['id_utilizador'],
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':telefone' => $dados['telefone'] ?? null,
            ':morada' => $dados['morada'] ?? null,
            ':data_nascimento' => $dados['data_nascimento'] ?? null,
            ':genero' => $dados['genero'] ?? null,
            ':estado_civil' => $dados['estado_civil'] ?? null,
            ':nif' => $dados['nif'] ?? null,
            ':niss' => $dados['niss'] ?? null,
            ':numero_mecanografico' => $dados['numero_mecanografico'] ?? null,
            ':nib' => $dados['nib'] ?? null,
            ':numero_dependentes' => $dados['numero_dependentes'],
            ':habilitacoes' => $dados['habilitacoes'] ?? null,
            ':contacto_emergencia' => $dados['contacto_emergencia'] ?? null,
            ':relacao_emergencia' => $dados['relacao_emergencia'] ?? null,
            ':telemovel_emergencia' => $dados['telemovel_emergencia'] ?? null
        ];

        // Adicionar campos ao SQL apenas se tiverem valor
        foreach ($valores as $campo => $valor) {
            if ($valor !== null && $campo !== ':id_utilizador') {
                $campos[] = substr($campo, 1) . ' = ' . $campo;
            }
        }

        // Montar a query dinamicamente
        $sql = "UPDATE Colaborador SET " . implode(', ', $campos) . " WHERE id_utilizador = :id_utilizador";
        
        // Remover campos nulos dos valores
        $valores = array_filter($valores, function($value) {
            return $value !== null;
        });

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($valores);
        } catch (PDOException $e) {
            // Se o erro for de duplicidade de NIF, ignorar e continuar
            if (strpos($e->getMessage(), '1062 Duplicate entry') !== false && strpos($e->getMessage(), 'nif') !== false) {
                // Remover o NIF dos valores e tentar novamente
                unset($valores[':nif']);
                
                // Remontar a query sem o NIF
                $campos = array_filter($campos, function($campo) {
                    return strpos($campo, 'nif') === false;
                });
                
                $sql = "UPDATE Colaborador SET " . implode(', ', $campos) . " WHERE id_utilizador = :id_utilizador";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute($valores);
            }
            throw $e;
        }
    }

    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM Colaborador";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function contarAdmissoesPorPeriodo($dataInicio, $dataFim) {
        // Se não existe coluna de data de admissão, apenas conte todos
        $sql = "SELECT COUNT(*) as total FROM Colaborador";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function obterDistribuicaoPorCargo() {
        $sql = "SELECT 
                    f.titulo as cargo,
                    COUNT(c.id_colaborador) as total,
                    ROUND((COUNT(c.id_colaborador) * 100.0) / (SELECT COUNT(*) FROM Colaborador), 2) as percentual
                FROM Colaborador c
                LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
                GROUP BY c.id_funcao, f.titulo
                ORDER BY total DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterDistribuicaoPorDepartamento() {
        $sql = "SELECT 
                    d.nome as departamento, 
                    COUNT(*) as total,
                    ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM colaborador), 2) as percentual
                FROM colaborador c
                JOIN departamento d ON c.id_departamento = d.id_departamento
                GROUP BY d.nome
                ORDER BY total DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarRecentes($limite = 5) {
        $sql = "SELECT 
                    c.id_colaborador,
                    c.nome,
                    c.email,
                    f.titulo as funcao,
                    c.data_entrada
                FROM Colaborador c
                LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
                ORDER BY c.data_entrada DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterDadosParaDashboard() {
        return [
            'total_colaboradores' => $this->contarTotal(),
            'admissoes_ultimo_mes' => $this->contarAdmissoesPorPeriodo(
                date('Y-m-01'),
                date('Y-m-t')
            ),
            'distribuicao_cargo' => $this->obterDistribuicaoPorCargo(),
            'distribuicao_departamento' => $this->obterDistribuicaoPorDepartamento(),
            'recentes' => $this->listarRecentes(5)
        ];
    }
    
    /**
     * Obtém a lista de colaboradores que fazem aniversário no mês especificado
     * 
     * @param string $mes Mês com dois dígitos (ex: '01' para janeiro)
     * @return array Lista de colaboradores aniversariantes
     */
    public function obterAniversariantesPorMes($mes) {
        $sql = "SELECT 
                    c.id_utilizador,
                    c.nome,
                    c.data_nascimento,
                    f.titulo as cargo,
                    d.nome as departamento
                FROM Colaborador c
                LEFT JOIN Funcao f ON c.id_funcao = f.id_funcao
                LEFT JOIN Departamento d ON c.id_departamento = d.id_departamento
                WHERE c.ativo = 1
                AND DATE_FORMAT(c.data_nascimento, '%m') = :mes
                ORDER BY DAY(c.data_nascimento) ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}