<?php
require_once '../config/config.php';

// Função para atualizar foto de perfil
function updateProfilePhoto($userId, $photoFileName) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE colaboradores SET foto = ? WHERE id = ?");
    $stmt->bind_param("si", $photoFileName, $userId);
    
    return $stmt->execute();
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
    
    return $stmt->execute();
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
