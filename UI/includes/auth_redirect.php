<?php
/**
 * Redirecionamento baseado em autenticação
 * 
 * Este arquivo deve ser incluído no início de todas as páginas que requerem autenticação
 */

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui o arquivo de permissões
require_once __DIR__ . '/permissions.php';

// Páginas que não requerem autenticação
$paginas_publicas = [
    'login.php',
    'recuperar_senha.php',
    'convidado.php'
];

// Obtém o nome do arquivo atual
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Se for uma página pública, não redireciona
if (in_array($pagina_atual, $paginas_publicas)) {
    return;
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil'])) {
    // Se não estiver logado, redireciona para o login
    $_SESSION['erro'] = "Por favor, faça login para acessar esta página.";
    header("Location: /LSIS-Equipa-9/UI/login.php");
    exit();
}

// Obtém o perfil do usuário
$perfil = $_SESSION['perfil'];

// Redireciona para a página inicial do perfil se necessário
if ($pagina_atual === 'dashboard.php') {
    switch ($perfil) {
        case PERFIL_COLABORADOR:
            header("Location: /LSIS-Equipa-9/UI/perfis/colaborador/dashboard.php");
            exit();
        case PERFIL_COORDENADOR:
            header("Location: /LSIS-Equipa-9/UI/perfis/coordenador/dashboard.php");
            exit();
        case PERFIL_RH:
            header("Location: /LSIS-Equipa-9/UI/perfis/rh/dashboard.php");
            exit();
        case PERFIL_ADMIN:
            header("Location: /LSIS-Equipa-9/UI/perfis/admin/dashboard.php");
            exit();
        case PERFIL_CONVIDADO:
        default:
            header("Location: /LSIS-Equipa-9/UI/convidado.php");
            exit();
    }
}

// Verifica permissão para a página atual
$pagina_relativa = str_replace('/LSIS-Equipa-9/UI/', '', $_SERVER['PHP_SELF']);

if (!verificarPermissao($pagina_relativa, $perfil)) {
    // Se não tiver permissão, redireciona para o dashboard do perfil
    $_SESSION['erro'] = "Você não tem permissão para acessar esta página.";
    
    switch ($perfil) {
        case PERFIL_COLABORADOR:
            header("Location: /LSIS-Equipa-9/UI/perfis/colaborador/dashboard.php");
            break;
        case PERFIL_COORDENADOR:
            header("Location: /LSIS-Equipa-9/UI/perfis/coordenador/dashboard.php");
            break;
        case PERFIL_RH:
            header("Location: /LSIS-Equipa-9/UI/perfis/rh/dashboard.php");
            break;
        case PERFIL_ADMIN:
            header("Location: /LSIS-Equipa-9/UI/perfis/admin/dashboard.php");
            break;
        case PERFIL_CONVIDADO:
        default:
            header("Location: /LSIS-Equipa-9/UI/convidado.php");
    }
    exit();
}
?>
