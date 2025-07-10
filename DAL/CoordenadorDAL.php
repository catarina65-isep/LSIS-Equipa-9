<?php
require_once __DIR__ . '/config.php';

class CoordenadorDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    /**
     * Obtém os dados do coordenador pelo ID do usuário
     * 
     * @param int $idUtilizador ID do usuário
     * @return array|false Retorna os dados do coordenador ou false se não encontrado
     */
    public function obterPorIdUtilizador($idUtilizador) {
        file_put_contents('debug.log', "Executando obterPorIdUtilizador para o ID: " . $idUtilizador . "\n", FILE_APPEND);
        
        $sql = "SELECT 
                    c.*, 
                    u.email, 
                    u.username,
                    u.id_utilizador,
                    c.id_coordenador,
                    c.id_equipa,
                    c.id_departamento,
                    c.cargo,
                    c.tipo_coordenacao,
                    c.permissoes_especificas,
                    c.data_inicio,
                    c.data_fim,
                    c.ativo,
                    c.observacoes
                FROM coordenador c 
                JOIN utilizador u ON c.id_utilizador = u.id_utilizador 
                WHERE c.id_utilizador = :id_utilizador
                AND c.ativo = 1
                AND u.ativo = 1";
                
        file_put_contents('debug.log', "SQL: " . $sql . "\n", FILE_APPEND);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_utilizador' => $idUtilizador]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            file_put_contents('debug.log', "Resultado da consulta: " . print_r($result, true) . "\n", FILE_APPEND);
            
            return $result;
        } catch (PDOException $e) {
            file_put_contents('debug.log', "Erro na consulta: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    
    /**
     * Obtém as equipes que um coordenador gerencia
     * 
     * @param int $idCoordenador ID do coordenador
     * @return array Lista de equipes gerenciadas pelo coordenador
     */
    public function obterEquipesGerenciadas($idCoordenador) {
        // Log para depuração
        error_log("Buscando equipes para o coordenador ID: " . $idCoordenador);
        
        // Primeiro, obtém o ID da equipe associada ao coordenador
        $sql = "SELECT id_equipa, id_utilizador FROM coordenador WHERE id_coordenador = :id_coordenador AND ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_coordenador' => $idCoordenador]);
        $coordenador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Dados do coordenador: " . print_r($coordenador, true));
        
        if (!$coordenador) {
            error_log("Coordenador não encontrado ou inativo: " . $idCoordenador);
            return [];
        }
        
        if (empty($coordenador['id_equipa'])) {
            error_log("Coordenador não está associado a nenhuma equipe: " . $idCoordenador);
            return [];
        }
        
        // Agora busca os dados da equipe
        $sql = "SELECT e.*, d.nome as nome_departamento
                FROM equipa e
                LEFT JOIN departamento d ON e.id_departamento = d.id_departamento
                WHERE e.id_equipa = :id_equipa
                AND e.ativo = 1";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipa' => $coordenador['id_equipa']]);
        
        $equipa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retorna como array para manter a compatibilidade
        return $equipa ? [$equipa] : [];
    }
    
    /**
     * Obtém os membros de uma equipe gerenciada pelo coordenador
     * 
     * @param int $idEquipa ID da equipe
     * @param int $idCoordenador ID do coordenador (para verificação de permissão)
     * @return array Lista de membros da equipe
     */
    public function obterMembrosEquipe($idEquipa, $idCoordenador) {
        // Primeiro verifica se o coordenador gerencia esta equipe
        $sqlVerificacao = "SELECT 1 FROM equipa WHERE id_equipa = :id_equipa AND id_coordenador = :id_coordenador AND ativo = 1";
        $stmt = $this->pdo->prepare($sqlVerificacao);
        $stmt->execute([
            ':id_equipa' => $idEquipa,
            ':id_coordenador' => $idCoordenador
        ]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Acesso negado: você não tem permissão para acessar esta equipe.');
        }
        
        // Se chegou aqui, o coordenador tem permissão para acessar a equipe
        $sql = "SELECT c.*, u.email, u.ultimo_login, u.ativo as conta_ativa,
                       f.titulo as funcao, e.nome as nome_equipa
                FROM colaborador c
                JOIN utilizador u ON c.id_utilizador = u.id_utilizador
                LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
                LEFT JOIN equipa e ON c.id_equipa = e.id_equipa
                WHERE c.id_equipa = :id_equipa
                AND c.estado = 'Ativo'
                ORDER BY c.nome, c.apelido";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipa' => $idEquipa]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém estatísticas da equipe para o dashboard do coordenador
     * 
     * @param int $idCoordenador ID do coordenador
     * @return array Estatísticas da equipe
     */
    public function obterEstatisticasEquipe($idCoordenador) {
        $estatisticas = [
            'total_membros' => 0,
            'distribuicao_cargos' => [],
            'aniversariantes_mes' => [],
            'equipes' => []
        ];
        
        // Obtém as equipes gerenciadas pelo coordenador
        $equipes = $this->obterEquipesGerenciadas($idCoordenador);
        
        foreach ($equipes as $equipa) {
            $idEquipa = $equipa['id_equipa'];
            
            // Conta total de membros por equipe
            $sqlTotal = "SELECT COUNT(*) as total 
                        FROM colaborador 
                        WHERE id_equipa = :id_equipa 
                        AND estado = 'Ativo'";
                        
            $stmt = $this->pdo->prepare($sqlTotal);
            $stmt->execute([':id_equipa' => $idEquipa]);
            $totalMembros = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $estatisticas['equipes'][$idEquipa] = [
                'nome' => $equipa['nome'],
                'total_membros' => $totalMembros,
                'membros' => []
            ];
            
            $estatisticas['total_membros'] += $totalMembros;
            
            // Obtém os membros da equipe
            $estatisticas['equipes'][$idEquipa]['membros'] = $this->obterMembrosEquipe($idEquipa, $idCoordenador);
            
            // Obtém distribuição por cargos
            $sqlCargos = "SELECT f.titulo as cargo, COUNT(*) as total
                         FROM colaborador c
                         LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
                         WHERE c.id_equipa = :id_equipa
                         AND c.estado = 'Ativo'
                         GROUP BY f.titulo";
                         
            $stmt = $this->pdo->prepare($sqlCargos);
            $stmt->execute([':id_equipa' => $idEquipa]);
            $estatisticas['equipes'][$idEquipa]['distribuicao_cargos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtém aniversariantes do mês
            $sqlAniversariantes = "SELECT id_colaborador, nome, apelido, 
                                 DATE_FORMAT(data_nascimento, '%d/%m') as data_nascimento,
                                 DATEDIFF(
                                     DATE_ADD(
                                         data_nascimento, 
                                         INTERVAL YEAR(CURDATE()) - YEAR(data_nascimento) + 
                                         IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(data_nascimento), 1, 0) YEAR
                                     ),
                                     CURDATE()
                                 ) as dias_para_aniversario
                                 FROM colaborador 
                                 WHERE id_equipa = :id_equipa
                                 AND estado = 'Ativo'
                                 AND MONTH(data_nascimento) = MONTH(CURDATE())
                                 ORDER BY DAY(data_nascimento)";
                                 
            $stmt = $this->pdo->prepare($sqlAniversariantes);
            $stmt->execute([':id_equipa' => $idEquipa]);
            $estatisticas['equipes'][$idEquipa]['aniversariantes_mes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $estatisticas;
    }
    
    /**
     * Remove um membro de uma equipe gerenciada pelo coordenador
     * 
     * @param int $idColaborador ID do colaborador a ser removido
     * @param int $idEquipa ID da equipe
     * @return bool Retorna true em caso de sucesso, false caso contrário
     */
    public function removerMembroEquipe($idColaborador, $idEquipa) {
        try {
            // Inicia uma transação
            $this->pdo->beginTransaction();
            
            // Primeiro, verifica se o colaborador pertence à equipe
            $sqlVerifica = "SELECT id_equipa FROM colaborador WHERE id_colaborador = :id_colaborador";
            $stmt = $this->pdo->prepare($sqlVerifica);
            $stmt->execute([':id_colaborador' => $idColaborador]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado || $resultado['id_equipa'] != $idEquipa) {
                throw new Exception('O colaborador não pertence a esta equipe.');
            }
            
            // Verifica se o colaborador é o coordenador da equipe
            $sqlVerificaCoordenador = "SELECT id_coordenador FROM equipa WHERE id_equipa = :id_equipa";
            $stmt = $this->pdo->prepare($sqlVerificaCoordenador);
            $stmt->execute([':id_equipa' => $idEquipa]);
            $equipa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($equipa && $equipa['id_coordenador'] == $idColaborador) {
                throw new Exception('Não é possível remover o coordenador da própria equipe.');
            }
            
            // Remove o colaborador da equipe (define id_equipa como NULL)
            $sqlRemove = "UPDATE colaborador SET id_equipa = NULL WHERE id_colaborador = :id_colaborador";
            $stmt = $this->pdo->prepare($sqlRemove);
            $stmt->execute([':id_colaborador' => $idColaborador]);
            
            // Registra a alteração no histórico
            $sqlHistorico = "INSERT INTO historico_equipa (id_equipa, id_colaborador, acao, descricao, data_acao, id_utilizador)
                           VALUES (:id_equipa, :id_colaborador, 'remocao', 'Colaborador removido da equipe', NOW(), :id_utilizador)";
            
            $stmt = $this->pdo->prepare($sqlHistorico);
            $stmt->execute([
                ':id_equipa' => $idEquipa,
                ':id_colaborador' => $idColaborador,
                ':id_utilizador' => $_SESSION['utilizador_id'] ?? null
            ]);
            
            // Confirma a transação
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            // Desfaz a transação em caso de erro
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Erro ao remover membro da equipe: ' . $e->getMessage());
            return false;
        }
    }
}
