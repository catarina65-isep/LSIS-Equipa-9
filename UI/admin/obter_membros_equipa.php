<?php
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../includes/verificar_acesso.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID da equipe inválido']);
    exit;
}

$equipaId = (int)$_GET['id'];

try {
    $equipaBLL = new EquipaBLL();
    $equipa = $equipaBLL->obterEquipa($equipaId);
    
    if (!$equipa) {
        http_response_code(404);
        echo json_encode(['erro' => 'Equipe não encontrada']);
        exit;
    }
    
    // Retorna os membros da equipe
    echo json_encode($equipa['membros'] ?? []);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar membros da equipe: ' . $e->getMessage()]);
}
