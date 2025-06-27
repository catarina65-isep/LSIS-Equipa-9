<?php
// Iniciar a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/login.php');
    exit();
}

// Verificar se o usuário tem permissão de administrador (id_perfilacesso = 1)
if (!isset($_SESSION['id_perfilacesso']) || $_SESSION['id_perfilacesso'] != 1) {
    // Se não for administrador, redireciona para a página inicial
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/index.php');
    exit();
}
?>
