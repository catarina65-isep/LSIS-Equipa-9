<?php
require_once '../config/config.php';

// Função para atualizar foto de perfil
function updateProfilePhoto($userId, $photoFileName) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE colaboradores SET foto = ? WHERE id = ?");
    $stmt->bind_param("si", $photoFileName, $userId);
    
    return $stmt->execute();
}

// Processar atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se é uma atualização de perfil
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data && isset($data['nome'])) {
        $userId = $_SESSION['usuario_id'];
        
        // Atualizar dados do perfil
        $result = updateProfileData($userId, $data);
        
        // Atualizar header com as informações do usuário
        if ($result['success']) {
            $user = $result['data'];
            echo json_encode($result);
            exit;
        }
    }
}

// Função para atualizar dados do perfil
function updateProfileData($userId, $data) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE colaboradores 
        SET nome = ?, email = ?, nif = ?, 
            data_nascimento = ?, morada = ?, 
            codigo_postal = ?, localidade = ?, 
            telefone = ?, observacoes = ? 
        WHERE id = ?
    ");
    
    $observacoes = isset($data['observacoes']) ? $data['observacoes'] : '';
    
    $stmt->bind_param("sssssssssi", 
        $data['nome'], 
        $data['email'], 
        $data['nif'], 
        $data['dataNascimento'], 
        $data['morada'], 
        $data['codigoPostal'], 
        $data['localidade'], 
        $data['telefone'], 
        $observacoes,
        $userId
    );
    
    if ($stmt->execute()) {
        // Retorna as informações atualizadas
        $stmt = $conn->prepare("SELECT nome, email, telefone FROM colaboradores WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        return array(
            'success' => true,
            'data' => $user
        );
    }
    
    return array(
        'success' => false,
        'message' => 'Erro ao atualizar perfil'
    );
}

// Função para obter dados do perfil
function getProfileData($userId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM colaboradores WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
