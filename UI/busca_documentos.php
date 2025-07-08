<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Verifica se os parâmetros são válidos
if (!isset($_GET['prefix']) || !isset($_GET['utilizador_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit;
}

$prefix = $_GET['prefix'];
$utilizador_id = $_GET['utilizador_id'];

// Diretório de uploads
$uploadDir = __DIR__ . '/../uploads/documentos/';

// Buscar arquivos com o prefixo e ID do usuário
$files = glob($uploadDir . $prefix . '_' . $utilizador_id . '_*');

if (!empty($files)) {
    // Encontrar o arquivo mais recente
    $latestFile = array_reduce($files, function($a, $b) {
        return filemtime($a) > filemtime($b) ? $a : $b;
    });
    
    $fileName = basename($latestFile);
    $uploadDate = date('Y-m-d H:i:s', filemtime($latestFile));
    
    echo json_encode([
        'file' => $fileName,
        'uploadDate' => $uploadDate
    ]);
} else {
    echo json_encode(['file' => null]);
}
