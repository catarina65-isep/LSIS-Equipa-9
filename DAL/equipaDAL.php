<?php
require_once __DIR__ . '/config.php';

class EquipaDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    /**
     * Obtém a instância PDO
     * 
     * @return \PDO
     */
    public function getPDO() {
        return $this->pdo;
    }

    public function criarEquipa($nome, $descricao, $coordenadorId, $idDepartamento = null, $idEquipaPai = null, $nivel = 1) {
        // Verificar se a conexão com o banco de dados está ativa
        if (!$this->pdo) {
            throw new Exception('Não foi possível conectar ao banco de dados');
        }
        
        error_log('Tentando inserir equipe no banco de dados: ' . print_r([
            'nome' => $nome,
            'descricao' => $descricao,
            'id_coordenador' => $coordenadorId,
            'id_departamento' => $idDepartamento,
            'id_equipa_pai' => $idEquipaPai,
            'nivel' => $nivel
        ], true));
        
        // Inserir a equipe
        $sql = "INSERT INTO equipa (
                    nome, 
                    descricao, 
                    id_departamento, 
                    id_equipa_pai, 
                    nivel, 
                    id_coordenador, 
                    ativo, 
                    data_criacao
                ) VALUES (
                    :nome, 
                    :descricao, 
                    :id_departamento, 
                    :id_equipa_pai, 
                    :nivel, 
                    :id_coordenador, 
                    1, 
                    NOW()
                )";
                
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':id_departamento' => $idDepartamento,
            ':id_equipa_pai' => $idEquipaPai,
            ':nivel' => $nivel,
            ':id_coordenador' => $coordenadorId
        ];
        
        error_log('SQL: ' . $sql);
        error_log('Parâmetros: ' . print_r($params, true));
        
        $result = $stmt->execute($params);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Erro ao executar a consulta: ' . ($errorInfo[2] ?? 'Erro desconhecido'));
        }
        
        $equipaId = $this->pdo->lastInsertId();
        error_log('Equipa criada com sucesso. ID: ' . $equipaId);
        
        // Adiciona o coordenador como membro da equipe
        $this->adicionarMembroEquipa($equipaId, $coordenadorId, true);
        
        return $equipaId;
    }

    public function adicionarMembroEquipa($equipaId, $utilizadorId, $coordenador = false) {
        try {
            $sql = "INSERT INTO equipa_membros (
                        equipa_id, 
                        utilizador_id, 
                        coordenador,
                        data_entrada,
                        ativo
                    ) VALUES (
                        :equipa_id, 
                        :utilizador_id, 
                        :coordenador,
                        NOW(),
                        1
                    ) ON DUPLICATE KEY UPDATE 
                        coordenador = :coordenador_update,
                        ativo = 1,
                        data_entrada = IF(ativo = 0, NOW(), data_entrada)";
            
            error_log('SQL adicionarMembroEquipa: ' . $sql);
            error_log('Parâmetros: ' . print_r([
                ':equipa_id' => $equipaId,
                ':utilizador_id' => $utilizadorId,
                ':coordenador' => $coordenador ? 1 : 0
            ], true));
            
            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':equipa_id' => $equipaId,
                ':utilizador_id' => $utilizadorId,
                ':coordenador' => $coordenador ? 1 : 0,
                ':coordenador_update' => $coordenador ? 1 : 0
            ];
            
            error_log('Parâmetros finais: ' . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log('Erro ao adicionar membro à equipe: ' . ($errorInfo[2] ?? 'Erro desconhecido'));
                throw new Exception('Erro ao adicionar membro à equipe: ' . ($errorInfo[2] ?? 'Erro desconhecido'));
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log('Erro PDO ao adicionar membro à equipe: ' . $e->getMessage());
            throw new Exception('Erro ao adicionar membro à equipe: ' . $e->getMessage());
        }
    }

    public function removerMembroEquipa($equipaId, $utilizadorId) {
        try {
            // Em vez de excluir, vamos marcar como inativo para manter o histórico
            $sql = "UPDATE equipa_membros 
                    SET ativo = 0 
                    WHERE equipa_id = :equipa_id 
                    AND utilizador_id = :utilizador_id";
                    
            error_log('SQL removerMembroEquipa: ' . $sql);
            error_log('Parâmetros: ' . print_r([
                ':equipa_id' => $equipaId,
                ':utilizador_id' => $utilizadorId
            ], true));
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':equipa_id' => $equipaId,
                ':utilizador_id' => $utilizadorId
            ]);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log('Erro ao remover membro da equipe: ' . ($errorInfo[2] ?? 'Erro desconhecido'));
                throw new Exception('Erro ao remover membro da equipe: ' . ($errorInfo[2] ?? 'Erro desconhecido'));
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log('Erro PDO ao remover membro da equipe: ' . $e->getMessage());
            throw new Exception('Erro ao remover membro da equipe: ' . $e->getMessage());
        }
    }

    public function obterEquipaPorId($id) {
        $sql = "SELECT e.*, u.username as coordenador_username 
                FROM equipa e 
                LEFT JOIN utilizador u ON e.coordenador_id = u.id_utilizador 
                WHERE e.id_equipa = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $equipa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($equipa) {
            $equipa['membros'] = $this->obterMembrosEquipa($id);
        }

        return $equipa;
    }

    public function obterTodasEquipas() {
        $sql = "SELECT e.*, u.username as coordenador_username 
                FROM equipa e 
                LEFT JOIN utilizador u ON e.id_coordenador = u.id_utilizador 
                WHERE e.ativo = 1
                ORDER BY e.nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterMembrosEquipa($equipaId) {
        $sql = "SELECT u.id_utilizador as id, u.username as nome, u.email, u.ativo 
                FROM utilizador u 
                JOIN equipa_membros em ON u.id_utilizador = em.utilizador_id 
                WHERE em.equipa_id = :equipa_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipa_id' => $equipaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém o coordenador de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @return array|false Dados do coordenador ou false se não encontrado
     */
    public function obterCoordenador($equipaId) {
        $sql = "SELECT u.id_utilizador as id, u.username as nome, u.email, u.ativo 
                FROM utilizador u 
                JOIN equipa e ON u.id_utilizador = e.coordenador_id 
                WHERE e.id = :equipa_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipa_id' => $equipaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém as subequipas de uma equipa
     * 
     * @param int $equipaPaiId ID da equipa pai
     * @return array Lista de subequipas
     */
    public function obterSubequipas($equipaPaiId) {
        // Implementação simplificada - retorna array vazio
        // Pois não há suporte a hierarquia na tabela equipa
        return [];
    }

    public function atualizarEquipa($id, $nome, $descricao, $coordenadorId) {
        error_log('Atualizando equipa no DAL: ' . print_r([
            'id' => $id,
            'nome' => $nome,
            'descricao' => $descricao,
            'coordenador_id' => $coordenadorId
        ], true));
        
        try {
            $this->pdo->beginTransaction();
            
            $sql = "UPDATE equipa SET nome = :nome, descricao = :descricao, coordenador_id = :coordenador_id WHERE id_equipa = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':coordenador_id' => $coordenadorId,
                ':id' => $id
            ]);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log('Erro ao atualizar equipa: ' . print_r($error, true));
                throw new Exception('Erro ao atualizar a equipa no banco de dados: ' . $error[2]);
            }
            
            $this->pdo->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Erro na transação de atualização de equipa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function excluirEquipa($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Primeiro, remove os membros da equipa
            $sql = "DELETE FROM equipa_membros WHERE equipa_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Depois, remove a equipa
            $sql = "DELETE FROM equipa WHERE id_equipa = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function obterEquipasPorMembro($utilizadorId) {
        $sql = "SELECT e.* 
                FROM equipa e 
                JOIN equipa_membros em ON e.id = em.equipa_id 
                WHERE em.utilizador_id = :utilizador_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':utilizador_id' => $utilizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Conta quantos coordenadores uma equipa possui
     * 
     * @param int $equipaId ID da equipa
     * @return int Número de coordenadores
     */
    public function contarCoordenadores($equipaId) {
        $sql = "SELECT COUNT(*) as total FROM equipa WHERE coordenador_id IS NOT NULL AND id_equipa = :equipa_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipa_id' => $equipaId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    
    /**
     * Verifica se um utilizador é membro de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do utilizador
     * @return bool True se for membro, false caso contrário
     */
    public function verificarMembroEquipa($equipaId, $utilizadorId) {
        $sql = "SELECT COUNT(*) as total FROM equipa_membros 
                WHERE equipa_id = :equipa_id AND utilizador_id = :utilizador_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':equipa_id' => $equipaId,
            ':utilizador_id' => $utilizadorId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'] > 0;
    }
    
    /**
     * Conta o número de membros de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @return int Número de membros
     */
    public function contarMembrosEquipa($equipaId) {
        $sql = "SELECT COUNT(*) as total FROM equipa_membros 
                WHERE equipa_id = :equipa_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipa_id' => $equipaId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Define o coordenador de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do novo coordenador
     * @return bool True se a operação for bem-sucedida, false caso contrário
     */
    public function definirCoordenador($equipaId, $utilizadorId) {
        try {
            $this->pdo->beginTransaction();
            
            // Primeiro, atualiza o coordenador na tabela de equipa
            $sql = "UPDATE equipa SET coordenador_id = :coordenador_id WHERE id_equipa = :equipa_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':coordenador_id' => $utilizadorId,
                ':equipa_id' => $equipaId
            ]);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log('Erro ao definir coordenador: ' . print_r($error, true));
                throw new Exception('Erro ao definir o coordenador: ' . $error[2]);
            }
            
            // Garante que o coordenador é membro da equipa
            if (!$this->verificarMembroEquipa($equipaId, $utilizadorId)) {
                $this->adicionarMembroEquipa($equipaId, $utilizadorId);
            }
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Erro na transação de definição de coordenador: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Conta o número total de equipes ativas
     * 
     * @return int Número total de equipes ativas
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM equipa WHERE ativo = 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    
    /**
     * Lista todas as equipes ativas
     * 
     * @return array Lista de equipes
     */
    public function listarTodas() {
        $sql = "SELECT * FROM equipa WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém a distribuição de colaboradores por equipe
     * 
     * @return array Distribuição de colaboradores por equipe
     */
    public function obterDistribuicaoPorEquipa() {
        try {
            // Verifica se as tabelas necessárias existem
            $checkEquipa = $this->pdo->query("SHOW TABLES LIKE 'equipa'");
            $checkEquipaMembros = $this->pdo->query("SHOW TABLES LIKE 'equipa_membros'");
            
            if ($checkEquipa->rowCount() === 0 || $checkEquipaMembros->rowCount() === 0) {
                // Se alguma das tabelas não existir, retorna um array vazio
                return [
                    [
                        'nome_equipa' => 'Equipas não configuradas',
                        'total_colaboradores' => 0,
                        'percentual' => 100.00
                    ]
                ];
            }
            
            // Verifica se a coluna 'ativo' existe na tabela equipa
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM equipa LIKE 'ativo'");
            $whereClause = $checkColumn->rowCount() > 0 ? "WHERE e.ativo = 1" : "";
            
            $sql = "SELECT 
                        e.nome as nome_equipa,
                        COUNT(em.utilizador_id) as total_colaboradores,
                        ROUND((COUNT(em.utilizador_id) * 100.0) / 
                            NULLIF((SELECT COUNT(*) FROM equipa_membros WHERE ativo = 1), 0), 2) as percentual
                    FROM equipa e
                    LEFT JOIN equipa_membros em ON e.id = em.equipa_id " . 
                    ($checkColumn->rowCount() > 0 ? "AND em.ativo = 1" : "") . "
                    $whereClause
                    GROUP BY e.id, e.nome
                    HAVING total_colaboradores > 0
                    ORDER BY total_colaboradores DESC";
            
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Se não houver resultados, retorna um valor padrão
            if (empty($result)) {
                return [
                    [
                        'nome_equipa' => 'Sem equipes cadastradas',
                        'total_colaboradores' => 0,
                        'percentual' => 0.00
                    ]
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            // Log do erro para depuração
            error_log("Erro em obterDistribuicaoPorEquipa: " . $e->getMessage());
            
            // Retorna um array vazio para evitar quebrar a aplicação
            return [
                [
                    'nome_equipa' => 'Erro ao carregar equipes',
                    'total_colaboradores' => 0,
                    'percentual' => 0.00
                ]
            ];
        }
    }
    
    /**
     * Conta o número de membros de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return int Número de membros
     */
    public function contarMembros($idEquipa) {
        $sql = "SELECT COUNT(*) as total 
                FROM equipa_membros 
                WHERE equipa_id = :id_equipa";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipa' => $idEquipa]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['total'];
    }
    
    /**
     * Conta o número de membros de uma equipe por status
     * 
     * @param int $idEquipa ID da equipe
     * @param string $status Status do colaborador
     * @return int Número de membros com o status especificado
     */
    public function contarMembrosPorStatus($idEquipa, $status) {
        $sql = "SELECT COUNT(*) as total 
                FROM equipa_membros em
                JOIN utilizador u ON em.utilizador_id = u.id_utilizador
                WHERE em.equipa_id = :id_equipa 
                AND u.ativo = :ativo";
        
        // Mapear o status para o valor booleano correspondente
        $ativo = ($status === 'Ativo') ? 1 : 0;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_equipa' => $idEquipa,
            ':ativo' => $ativo
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    
    /**
     * Obtém a data da última atualização de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return string Data da última atualização
     */
    public function obterUltimaAtualizacao($idEquipa) {
        $sql = "SELECT data_criacao 
                FROM equipa 
                WHERE id_equipa = :id_equipa";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipa' => $idEquipa]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['data_criacao'] : date('Y-m-d H:i:s');
    }
}
