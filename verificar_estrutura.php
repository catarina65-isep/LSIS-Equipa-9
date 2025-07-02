<?php
require_once __DIR__ . '/DAL/config.php';

try {
    $pdo = Database::getInstance();
    
    // Verificar estrutura da tabela utilizador
    $stmt = $pdo->query("SHOW COLUMNS FROM utilizador");
    $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Estrutura da tabela utilizador:</h3>";
    echo "<pre>";
    print_r($colunas);
    echo "</pre>";
    
    // Listar os primeiros 5 usuários com as colunas corretas
    $stmt = $pdo->query("SELECT * FROM utilizador LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Primeiros 5 usuários:</h3>";
    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";
    
} catch (PDOException $e) {
    die("Erro ao acessar o banco de dados: " . $e->getMessage());
}
?>
