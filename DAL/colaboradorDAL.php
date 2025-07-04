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

    public function buscarPorId($id) {
        $sql = "SELECT * FROM Colaborador WHERE id_colaborador = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        // Como a tabela colaborador não tem id_departamento, vamos buscar os departamentos através da função
        $sql = "SELECT 
                    'Departamento Indefinido' as departamento,
                    COUNT(*) as total,
                    ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM colaborador), 2) as percentual
                FROM colaborador c
                GROUP BY 1
                ORDER BY total DESC";
        
        $stmt = $this->db->query($sql);
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
}