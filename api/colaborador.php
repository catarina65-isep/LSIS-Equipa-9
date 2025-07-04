<?php
require_once '../autoload.php';

header('Content-Type: application/json');

// Verificar autenticação
$token = isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : null;
if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Token de autenticação não fornecido']);
    exit;
}

// Verificar sessão
session_start();
if (!isset($_SESSION['utilizador_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Verificar se é colaborador
if ($_SESSION['id_perfilacesso'] != 4) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

try {
    // Conectar ao banco de dados
    require_once '../config/database.php';
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Buscar dados do colaborador
    $stmt = $pdo->prepare("SELECT * FROM colaboradores WHERE id_utilizador = :id_utilizador");
    $stmt->execute(['id_utilizador' => $_SESSION['utilizador_id']]);
    $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$colaborador) {
        http_response_code(404);
        echo json_encode(['error' => 'Colaborador não encontrado']);
        exit;
    }
    
    // Buscar dados do usuário
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['utilizador_id']]);
    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Combinar os dados
    $dados = array_merge($colaborador, $utilizador);
    
    echo json_encode($dados);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
