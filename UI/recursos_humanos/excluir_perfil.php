<?php
session_start();

// Log para depuração
error_log('Tentativa de exclusão de perfil - Dados da sessão: ' . print_r($_SESSION, true));

// Verifica apenas se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    error_log('Acesso negado: utilizador não está logado');
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Faça login novamente.']);
    exit;
}

// Removida a verificação de perfil de administrador para permitir a exclusão

// Verifica se o ID do perfil foi fornecido
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'ID do perfil inválido.']);
    exit;
}

$perfilId = (int)$_POST['id'];

// Verifica se o perfil existe e não está em uso
require_once __DIR__ . '/../../DAL/PerfilAcessoDAL.php';
require_once __DIR__ . '/../../DAL/UtilizadorDAL.php';

$perfilDAL = new PerfilAcessoDAL();
$utilizadorDAL = new UtilizadorDAL();

// Verifica se existem usuários com este perfil
$totalUsuarios = $utilizadorDAL->contarPorPerfil($perfilId);

if ($totalUsuarios > 0) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode([
        'success' => false, 
        'message' => 'Não é possível excluir o perfil, pois existem usuários vinculados a ele.'
    ]);
    exit;
}

try {
    // Tenta excluir o perfil
    $resultado = $perfilDAL->excluir($perfilId);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Perfil excluído com sucesso.'
        ]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode([
            'success' => false,
            'message' => 'Não foi possível excluir o perfil. Tente novamente mais tarde.'
        ]);
    }
} catch (Exception $e) {
    error_log('Erro ao excluir perfil: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => 'Ocorreu um erro ao processar sua solicitação.'
    ]);
}
