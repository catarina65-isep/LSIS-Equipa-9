<?php
require_once __DIR__ . '/includes/session.php';

// Habilita a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../BLL/loginBLL.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    

    if (empty($email) || empty($senha)) {
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

            // Redireciona com base no perfil do usuário
            if ($_SESSION['id_perfilacesso'] == 1) { // Administrador
                header('Location: admin/dashboard.php');
            } elseif ($_SESSION['id_perfilacesso'] == 2) { // Recursos Humanos
                header('Location: rh.php');
            } elseif ($_SESSION['id_perfilacesso'] == 3) { // Coordenador
                header('Location: coordenador.php');
            } elseif ($_SESSION['id_perfilacesso'] == 4) { // Colaborador
                header('Location: colaborador.php');
            } else {
                header('Location: ../index.php');
            }
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
