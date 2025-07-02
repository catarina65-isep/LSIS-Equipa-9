<?php
require_once __DIR__ . '/DAL/config.php';

try {
    $pdo = Database::getInstance();
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'utilizador'");
    $tabelaExiste = $stmt->rowCount() > 0;
    
    if (!$tabelaExiste) {
        die("A tabela 'utilizador' não existe no banco de dados.");
    }
    
    // Contar usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos FROM utilizador");
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total de usuários: " . $dados['total'] . "<br>";
    echo "Usuários ativos: " . $dados['ativos'] . "<br>";
    
    // Listar os primeiros 5 usuários
    $stmt = $pdo->query("SELECT id_utilizador, nome, username, email, ativo FROM utilizador ORDER BY id_utilizador LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Primeiros 5 usuários:</h3>";
    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";
    
} catch (PDOException $e) {
    die("Erro ao acessar o banco de dados: " . $e->getMessage());
}
?>
