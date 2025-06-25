<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $tipo_usuario = (int)($_POST['tipo_usuario'] ?? 0);

    if (empty($email) || empty($senha) || $tipo_usuario === 0) {
        $_SESSION['erro'] = 'Por favor, preencha todos os campos.';
        header('Location: login.php');
        exit;
    }

    try {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("
            SELECT u.*, p.descricao as perfil 
            FROM utilizador u
            JOIN perfilacesso p ON u.id_perfil_acesso = p.id_perfil_acesso
            WHERE u.email = ? AND u.id_perfil_acesso = ? AND u.ativo = 1
            LIMIT 1
        ");
        
        $stmt->execute([$email, $tipo_usuario]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['password_hash'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id_utilizador'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_nome'] = $usuario['username'];
            $_SESSION['usuario_tipo'] = $tipo_usuario;
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            
            // Redirecionar conforme o perfil
            switch ($tipo_usuario) {
                case 1: header('Location: colaborador/dashboard.php'); break;
                case 2: header('Location: coordenador/dashboard.php'); break;
                case 3: header('Location: rh/dashboard.php'); break;
                case 4: header('Location: admin/dashboard.php'); break;
                default: header('Location: index.php');
            }
            exit;
        } else {
            $_SESSION['erro'] = 'Credenciais inválidas. Verifique seu email e senha.';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log('Erro no login: ' . $e->getMessage());
        $_SESSION['erro'] = 'Ocorreu um erro ao processar seu login. Tente novamente.';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}