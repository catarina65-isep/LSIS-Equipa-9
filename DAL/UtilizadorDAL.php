<?php
require_once __DIR__ . '/config.php';

class UtilizadorDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function listarTodos() {
        $sql = "SELECT id_utilizador, nome, email, username, ativo, data_criacao 
                FROM utilizador 
                WHERE ativo = 1 
                ORDER BY nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($id) {
        $sql = "SELECT id_utilizador, nome, email, username, ativo, data_criacao 
                FROM utilizador 
                WHERE id_utilizador = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
