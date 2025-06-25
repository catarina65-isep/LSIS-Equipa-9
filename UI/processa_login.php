<?php
session_start();

// Habilita a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../BLL/loginBLL.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $tipo_usuario = filter_input(INPUT_POST, 'tipo_usuario', FILTER_SANITIZE_NUMBER_INT);

    if (empty($email) || empty($senha) || empty($tipo_usuario)) {
        $_SESSION['erro'] = 'Por favor, preencha todos os campos.';
        header('Location: login.php');
        exit;
    }

    try {
        $loginBLL = new LoginBLL();
        $usuario = $loginBLL->autenticar($email, $senha);

        if ($usuario) {
            // Define as variáveis de sessão
            $_SESSION['usuario_id'] = $usuario['id_utilizador'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Usuário';
            $_SESSION['id_perfilacesso'] = $usuario['id_perfilacesso'];
            $_SESSION['perfil_nome'] = $usuario['perfil'] ?? 'Usuário';

            // Redireciona para a página inicial
            header('Location: ../index.php');
            exit;
        } else {
            throw new Exception('Credenciais inválidas. Verifique seu email e senha.');
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = $e->getMessage();
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
