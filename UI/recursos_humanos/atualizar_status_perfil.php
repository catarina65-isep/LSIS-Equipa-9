<?php
session_start();

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit;
}

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Valida os dados de entrada
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || !isset($_POST['ativo'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

$perfilId = (int)$_POST['id'];
$ativo = (int)$_POST['ativo'] === 1 ? 1 : 0;

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../../BLL/PerfilAcessoBLL.php';

try {
    $perfilBLL = new PerfilAcessoBLL();
    
    // Verifica se o perfil existe
    $perfil = $perfilBLL->obterPorId($perfilId);
    
    if (!$perfil) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Perfil não encontrado.']);
        exit;
    }
    
    // Atualiza o status do perfil
    $resultado = $perfilBLL->atualizarStatus($perfilId, $ativo);
    
    if ($resultado) {
        echo json_encode([
            'success' => true, 
            'message' => 'Status do perfil atualizado com sucesso!',
            'data' => [
                'id' => $perfilId,
                'ativo' => $ativo
            ]
        ]);
    } else {
        throw new Exception('Não foi possível atualizar o status do perfil.');
    }
    
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar a requisição: ' . $e->getMessage()
    ]);
}
