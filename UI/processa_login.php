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

            // Log de depuração detalhado
            error_log('=== DADOS DO USUÁRIO LOGADO ===');
            error_log('ID: ' . $usuario['id_utilizador']);
            error_log('Email: ' . $usuario['email']);
            error_log('Nome: ' . ($usuario['nome'] ?? 'N/A'));
            error_log('ID Perfil: ' . $usuario['id_perfilacesso']);
            error_log('Perfil: ' . ($usuario['perfil'] ?? 'N/A'));
            error_log('Dados completos: ' . print_r($usuario, true));
            error_log('Dados da sessão: ' . print_r($_SESSION, true));

            // Redireciona com base no perfil do usuário
            switch ($_SESSION['id_perfilacesso']) {
                case 1: // Administrador
                    header('Location: admin/dashboard.php');
                    break;
                case 2: // Recursos Humanos
                    header('Location: rh.php');
                    break;
                case 3: // Coordenador
                    header('Location: coordenador.php');
                    break;
                case 4: // Colaborador
                    header('Location: colaborador.php');
                    break;
                default:
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
