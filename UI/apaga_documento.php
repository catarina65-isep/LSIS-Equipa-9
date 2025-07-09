<?php
session_start();

require_once __DIR__ . '/../BLL/ColaboradorBLL.php';

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verifica se o perfil é de colaborador
if ($_SESSION['id_perfilacesso'] != 4) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

try {
    // Recebe os dados
    $data = json_decode(file_get_contents('php://input'), true);
    $fileName = $data['fileName'] ?? '';
    $field = $data['field'] ?? '';

    // Verifica se temos os dados necessários
    if (!$fileName || !$field) {
        throw new Exception('Dados incompletos');
    }

    // Verifica se o arquivo existe e pertence ao usuário
    $uploadDir = __DIR__ . '/../uploads/documentos/';
    $filePath = $uploadDir . $fileName;
    
    if (!file_exists($filePath)) {
        throw new Exception('Arquivo não encontrado');
    }

    // Apaga o arquivo
    if (!unlink($filePath)) {
        throw new Exception('Erro ao apagar arquivo');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
