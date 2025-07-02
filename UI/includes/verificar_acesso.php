<?php
// A sessão já é iniciada no header.php
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/login.php');
    exit();
}

// Verificar se o usuário tem permissão de administrador ou RH (id_perfilacesso = 1 ou 2)
$permitirAcesso = false;

// Verificar se a sessão tem o perfil necessário
if (isset($_SESSION['id_perfilacesso'])) {
    // Administrador (1) ou RH (2) têm permissão
    if (in_array($_SESSION['id_perfilacesso'], [1, 2])) {
        $permitirAcesso = true;
    }
    
    // Coordenadores (3) podem visualizar, mas não editar
    if ($_SESSION['id_perfilacesso'] == 3) {
        // Verificar se é uma requisição AJAX para obter dados
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        // Se for uma requisição AJAX para obter dados, permitir
        if ($isAjax && isset($_POST['acao']) && $_POST['acao'] == 'obter_membros_equipa') {
            $permitirAcesso = true;
        } else {
            // Para outras páginas, redirecionar para o painel
            header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/dashboard.php');
            exit();
        }
    }
}

if (!$permitirAcesso) {
    // Se não tiver permissão, redireciona para a página inicial
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/index.php');
    exit();
}
?>
