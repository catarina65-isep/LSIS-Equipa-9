<?php
session_start();

// Habilita a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verifica se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método de requisição inválido');
    }

    // Verifica se os campos obrigatórios foram preenchidos
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        throw new Exception('Por favor, preencha todos os campos.');
    }

    // Inclui o arquivo de configuração do banco de dados
    require_once __DIR__ . '/DAL/database.php';
    require_once __DIR__ . '/DAL/LoginDAL.php';
    require_once __DIR__ . '/BLL/LoginBLL.php';

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    $loginBLL = new LoginBLL();
    $usuario = $loginBLL->autenticar($email, $senha);
    
    if (!$usuario) {
        throw new Exception('Falha na autenticação. Verifique suas credenciais.');
    }
    
    // Se chegou até aqui, a autenticação foi bem-sucedida
    $_SESSION['usuario_id'] = $usuario['id_utilizador'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['perfil'] = $usuario['perfil'];
    $_SESSION['id_perfilacesso'] = $usuario['id_perfilacesso'];
    
    // Redireciona com base no perfil
    $redirecionamento = match((int)$usuario['id_perfilacesso']) {
        1 => 'admin/dashboard.php',
        2 => 'rh/dashboard.php',
        3 => 'coordenador/dashboard.php',
        4 => 'colaborador/dashboard.php',
        default => 'index.php'
    };
    
    // Se o diretório de destino não existir, redireciona para uma página padrão
    if (!is_dir(dirname(__DIR__) . '/UI/' . dirname($redirecionamento))) {
        $redirecionamento = 'index.php';
    }
    
    header('Location: ' . $redirecionamento);
    exit();
    
} catch (Exception $e) {
    // Log do erro para depuração
    error_log('Erro no login: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Define a mensagem de erro na sessão
    $_SESSION['erro'] = $e->getMessage();
    
    // Redireciona de volta para a página de login
    header('Location: login.php');
    exit();
}
?>
