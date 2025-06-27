<?php
require_once __DIR__ . '/config.php';

class EquipaDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function criarEquipa($nome, $descricao, $coordenadorId) {
        $sql = "INSERT INTO equipas (nome, descricao, coordenador_id, data_criacao) VALUES (:nome, :descricao, :coordenador_id, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':coordenador_id' => $coordenadorId
        ]);
        
        if ($result) {
            $equipaId = $this->pdo->lastInsertId();
            // Adiciona o coordenador como membro da equipe
            $this->adicionarMembroEquipa($equipaId, $coordenadorId);
            return $equipaId;
        }
        
        return false;
    }

    public function adicionarMembroEquipa($equipaId, $utilizadorId) {
        $sql = "INSERT IGNORE INTO equipa_membros (equipa_id, utilizador_id, data_adesao) VALUES (:equipa_id, :utilizador_id, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipa_id' => $equipaId,
            ':utilizador_id' => $utilizadorId
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
                FROM equipas e 
                LEFT JOIN utilizador u ON e.coordenador_id = u.id_utilizador 
                WHERE e.id = :id";
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
                FROM equipas e 
                LEFT JOIN utilizador u ON e.coordenador_id = u.id_utilizador 
                ORDER BY e.nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterMembrosEquipa($equipaId) {
        $sql = "SELECT u.* FROM utilizador u 
                JOIN equipa_membros em ON u.id_utilizador = em.utilizador_id 
                WHERE em.equipa_id = :equipa_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipa_id' => $equipaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarEquipa($id, $nome, $descricao, $coordenadorId) {
        $sql = "UPDATE equipas SET nome = :nome, descricao = :descricao, coordenador_id = :coordenador_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':coordenador_id' => $coordenadorId,
            ':id' => $id
        ]);
    }

    public function excluirEquipa($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Primeiro, remove os membros da equipe
            $sql = "DELETE FROM equipa_membros WHERE equipa_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Depois, remove a equipe
            $sql = "DELETE FROM equipas WHERE id = :id";
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
        $sql = "SELECT e.* FROM equipas e 
                JOIN equipa_membros em ON e.id = em.equipa_id 
                WHERE em.utilizador_id = :utilizador_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':utilizador_id' => $utilizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
