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

    public function criarEquipa($nome, $descricao, $coordenadorId) {
        $sql = "INSERT INTO equipa (nome, descricao, id_coordenador, data_criacao) VALUES (:nome, :descricao, :id_coordenador, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':id_coordenador' => $coordenadorId
        ];
        
        $result = $stmt->execute($params);
        
        if ($result) {
            $equipaId = $this->pdo->lastInsertId();
            // Adiciona o coordenador como membro da equipa
            $this->adicionarMembroEquipa($equipaId, $coordenadorId);
            return $equipaId;
        }
        
        return false;
    }

    public function adicionarMembroEquipa($equipaId, $utilizadorId, $coordenador = false) {
        $sql = "INSERT INTO equipa_membros (equipa_id, utilizador_id, coordenador) 
                VALUES (:equipa_id, :utilizador_id, :coordenador)
                ON DUPLICATE KEY UPDATE coordenador = :coordenador";
                
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipa_id' => $equipaId,
            ':utilizador_id' => $utilizadorId,
            ':coordenador' => $coordenador ? 1 : 0
        ]);
    }

    public function removerMembroEquipa($equipaId, $utilizadorId) {
        $sql = "DELETE FROM equipa_membros WHERE equipa_id = :equipa_id AND utilizador_id = :utilizador_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipa_id' => $equipaId,
            ':utilizador_id' => $utilizadorId
        ]);
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
}
?>
