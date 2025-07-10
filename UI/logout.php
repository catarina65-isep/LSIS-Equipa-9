<?php
// Inicia a sessão
session_start();

// Verifica se há um usuário logado para registrar o logout
if (isset($_SESSION['utilizador_id'])) {
    try {
        require_once __DIR__ . '/../DAL/AtividadeDAL.php';
        $atividadeDAL = new AtividadeDAL();
        $atividadeDAL->registrarAtividade(
            $_SESSION['utilizador_id'],
            $_SESSION['utilizador_email'] ?? '',
            'logout',
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            'sucesso',
            'Logout realizado com sucesso'
        );
    } catch (Exception $e) {
        error_log("Erro ao registrar atividade de logout: " . $e->getMessage());
    }
}

// Remove todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: login.php');
exit;
?>
