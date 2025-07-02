<?php
session_start();
require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';

// Função para enviar resposta JSON e encerrar o script
function sendJsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Verificar se é uma requisição AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    if ($isAjax) {
        sendJsonResponse(false, 'Acesso não autorizado.');
    } else {
        $_SESSION['erro'] = 'Acesso não autorizado.';
        header('Location: ../login.php');
        exit;
    }
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        sendJsonResponse(false, 'Método de requisição inválido.');
    } else {
        $_SESSION['erro'] = 'Método de requisição inválido.';
        header('Location: perfis.php');
        exit;
    }
}

// Validar campos obrigatórios
$camposObrigatorios = ['usuario_id' => 'ID do usuário', 'nome' => 'Nome', 'email' => 'E-mail', 'username' => 'Nome de usuário', 'perfil' => 'Perfil de acesso'];
$erros = [];

foreach ($camposObrigatorios as $campo => $rotulo) {
    if (empty($_POST[$campo])) {
        $erros[$campo] = "O campo $rotulo é obrigatório.";
    }
}

// Validar e-mail
if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
    $erros['email'] = 'O endereço de e-mail não é válido.';
}

// Validar senha se fornecida
if (!empty($_POST['password'])) {
    if ($_POST['password'] !== ($_POST['confirm_password'] ?? '')) {
        $erros['password'] = 'As senhas não coincidem.';
    } elseif (strlen($_POST['password']) < 6) {
        $erros['password'] = 'A senha deve ter pelo menos 6 caracteres.';
    }
}

// Se houver erros, retornar
if (!empty($erros)) {
    if ($isAjax) {
        sendJsonResponse(false, 'Por favor, corrija os erros abaixo.', ['errors' => $erros]);
    } else {
        $_SESSION['erro'] = 'Por favor, corrija os erros no formulário.';
        $_SESSION['form_errors'] = $erros;
        header('Location: editar_perfil.php?id=' . urlencode($_POST['usuario_id']));
        exit;
    }
}

try {
    // Preparar dados para atualização
    $dadosAtualizacao = [
        'id_utilizador' => $_POST['usuario_id'],
        'nome' => trim($_POST['nome']),
        'email' => trim($_POST['email']),
        'username' => trim($_POST['username']),
        'id_perfilacesso' => (int)$_POST['perfil'],
        'ativo' => 1 // Sempre ativo por padrão
    ];

    // Adicionar senha apenas se for fornecida
    if (!empty($_POST['password'])) {
        $dadosAtualizacao['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $utilizadorBLL = new UtilizadorBLL();
    $resultado = $utilizadorBLL->atualizar($dadosAtualizacao);
    
    if ($resultado) {
        if ($isAjax) {
            sendJsonResponse(true, 'Perfil atualizado com sucesso!');
        } else {
            $_SESSION['sucesso'] = 'Perfil atualizado com sucesso!';
            header('Location: editar_perfil.php?id=' . urlencode($_POST['usuario_id']));
            exit;
        }
    } else {
        throw new Exception('Não foi possível atualizar o perfil.');
    }
} catch (Exception $e) {
    error_log('Erro ao atualizar perfil: ' . $e->getMessage());
    
    if ($isAjax) {
        sendJsonResponse(false, 'Ocorreu um erro ao atualizar o perfil: ' . $e->getMessage());
    } else {
        $_SESSION['erro'] = 'Ocorreu um erro ao atualizar o perfil. Por favor, tente novamente.';
        header('Location: editar_perfil.php?id=' . urlencode($_POST['usuario_id']));
        exit;
    }
}
