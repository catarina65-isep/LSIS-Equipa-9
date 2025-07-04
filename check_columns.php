<?php
require_once __DIR__ . '/DAL/Database.php';

try {
    $pdo = Database::getInstance();
    
    // Verificar colunas da tabela Colaborador
    $stmt = $pdo->query("SHOW COLUMNS FROM Colaborador");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Colunas da tabela Colaborador:\n";
    print_r($columns);
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
