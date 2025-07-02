<?php
require_once __DIR__ . '/DAL/config.php';

try {
    $pdo = Database::getInstance();
    
    // Verificar se a tabela colaboradores existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'colaboradores'");
    $tabelaExiste = $stmt->rowCount() > 0;
    
    if (!$tabelaExiste) {
        die("A tabela 'colaboradores' não existe no banco de dados.");
    }
    
    // Contar colaboradores
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM colaboradores");
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total de colaboradores: " . $dados['total'] . "<br>";
    
    // Listar os primeiros 5 colaboradores
    $stmt = $pdo->query("SELECT * FROM colaboradores ORDER BY id_colaborador LIMIT 5");
    $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Primeiros 5 colaboradores:</h3>";
    echo "<pre>";
    print_r($colaboradores);
    echo "</pre>";
    
} catch (PDOException $e) {
    die("Erro ao acessar o banco de dados: " . $e->getMessage());
}
?>
