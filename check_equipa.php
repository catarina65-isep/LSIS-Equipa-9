<?php
require_once __DIR__ . '/config.php';

$pdo = Database::getInstance();

try {
    // Verificar tabela equipa
    $stmt = $pdo->query("SELECT * FROM equipa WHERE ativo = 1");
    $equipas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Equipas Ativas:</h2>";
    echo "<pre>";
    print_r($equipas);
    echo "</pre>";
    
    // Verificar tabela coordenador
    $stmt = $pdo->query("SELECT * FROM coordenador WHERE ativo = 1");
    $coordenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Coordenadores Ativos:</h2>";
    echo "<pre>";
    print_r($coordenadores);
    echo "</pre>";
    
    // Verificar usuário atual
    if (isset($_SESSION['utilizador_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE id_utilizador = ?");
        $stmt->execute([$_SESSION['utilizador_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h2>Usuário Atual:</h2>";
        echo "<pre>";
        print_r($usuario);
        echo "</pre>";
        
        // Verificar se é coordenador
        $stmt = $pdo->prepare("SELECT * FROM coordenador WHERE id_utilizador = ? AND ativo = 1");
        $stmt->execute([$_SESSION['utilizador_id']]);
        $coordenador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h2>Dados do Coordenador:</h2>";
        echo "<pre>";
        print_r($coordenador);
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Erro:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
