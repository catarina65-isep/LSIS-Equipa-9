<?php
session_start();
require_once '../config/config.php';
require_once '../DAL/colaborador.php';

header('Content-Type: application/json');

// Função para validar se o usuário está autenticado e é colaborador
function validateUser() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 4) {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso não autorizado']);
        exit;
    }
    return $_SESSION['usuario_id'];
}

// Função para processar upload de foto
function uploadProfilePhoto($userId) {
    $uploadDir = '../uploads/profile_photos/';
    
    // Criar diretório se não existir
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Verificar se arquivo foi enviado
    if (!isset($_FILES['profilePhoto']) || $_FILES['profilePhoto']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Nenhum arquivo enviado']);
        exit;
    }

    $file = $_FILES['profilePhoto'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Verificar extensão do arquivo
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png');

    if (!in_array($fileExt, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Formato de arquivo não permitido']);
        exit;
    }

    // Verificar tamanho do arquivo (2MB máximo)
    if ($fileSize > 2097152) {
        http_response_code(400);
        echo json_encode(['error' => 'Arquivo muito grande (máximo 2MB)']);
        exit;
    }

    // Gerar nome único para o arquivo
    $newFileName = $userId . '_' . time() . '.' . $fileExt;
    $fileDestination = $uploadDir . $newFileName;

    // Mover arquivo
    if (move_uploaded_file($fileTmpName, $fileDestination)) {
        // Atualizar URL da foto no banco de dados
        $result = updateProfilePhoto($userId, $newFileName);
        if ($result) {
            echo json_encode(['success' => true, 'photo_url' => $newFileName]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar foto no banco de dados']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao fazer upload do arquivo']);
    }
}

// Função para atualizar dados do perfil
function updateProfileData($userId) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar campos obrigatórios
    $requiredFields = ['nome', 'email', 'nif', 'dataNascimento', 'morada', 'codigoPostal', 'localidade', 'telefone'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Campo obrigatório não preenchido: $field"]);
            exit;
        }
    }

    // Atualizar dados no banco de dados
    $result = updateProfileData($userId, $data);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar perfil']);
    }
}

// Rota para upload de foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePhoto'])) {
    $userId = validateUser();
    uploadProfilePhoto($userId);
}

// Rota para atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $userId = validateUser();
    updateProfileData($userId);
}

// Se nenhuma rota for atendida
http_response_code(404);
echo json_encode(['error' => 'Rota não encontrada']);
