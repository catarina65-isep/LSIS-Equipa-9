<?php
/**
 * Configuração de permissões por perfil
 * 
 * Define as permissões de acesso para cada tipo de perfil de usuário
 * 
 * Níveis de perfil:
 * 1 - Administrador
 * 2 - RH
 * 3 - Coordenador
 * 4 - Colaborador
 * 5 - Convidado
 */

// Níveis de acesso (baseado no banco de dados)
define('PERFIL_ADMIN', 1);
define('PERFIL_RH', 2);
define('PERFIL_COORDENADOR', 3);
define('PERFIL_COLABORADOR', 4);
define('PERFIL_CONVIDADO', 5);

/**
 * Verifica se o usuário tem permissão para acessar uma página
 * 
 * @param string $pagina Caminho da página a ser verificada
 * @param int $perfilId ID do perfil do usuário
 * @return bool True se tiver permissão, False caso contrário
 */
function verificarPermissao($pagina, $perfilId) {
    // Páginas públicas que não requerem autenticação
    $paginas_publicas = [
        'login.php',
        'recuperar_senha.php',
        'convidado.php'
    ];
    
    // Se for uma página pública, permite acesso
    if (in_array(basename($pagina), $paginas_publicas)) {
        return true;
    }
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['utilizador_id']) || !isset($perfilId)) {
        return false;
    }
    
    // Mapeamento de permissões por perfil
    $permissoes_por_perfil = [
        PERFIL_ADMIN => [
            'todos' => true, // Acesso total
        ],
        PERFIL_RH => [
            'recursos_humanos/' => true,
            'colaborador/' => true,
            'equipas/' => true,
            'relatorios/' => true,
            'documentos/' => true,
        ],
        PERFIL_COORDENADOR => [
            'equipas/' => true,
            'colaborador/ver_equipa' => true,
            'colaborador.php' => true,
            'relatorios/equipa' => true,
        ],
        PERFIL_COLABORADOR => [
            'colaborador/meu_perfil' => true,
            'documentos/meus' => true,
        ],
        PERFIL_CONVIDADO => [
            'convidado/' => true,
        ]
    ];
    
    // Admin tem acesso total
    if ($perfilId == PERFIL_ADMIN) {
        return true;
    }
    
    // Verifica se o perfil tem permissões definidas
    if (!isset($permissoes_por_perfil[$perfilId])) {
        return false;
    }
    
    // Verifica as permissões específicas do perfil
    foreach ($permissoes_por_perfil[$perfilId] as $caminho => $temAcesso) {
        if (strpos($pagina, $caminho) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Redireciona para a página de login com uma mensagem de erro
 */
function redirecionarParaLogin($mensagem = '') {
    if (!empty($mensagem)) {
        $_SESSION['erro'] = $mensagem;
    } else {
        $_SESSION['erro'] = "Você precisa fazer login para acessar esta página.";
    }
    header("Location: /LSIS-Equipa-9/UI/login.php");
    exit();
}

/**
 * Redireciona para a página de acesso negado
 */
function redirecionarAcessoNegado() {
    $_SESSION['erro'] = "Você não tem permissão para acessar esta página.";
    header("Location: /LSIS-Equipa-9/UI/acesso_negado.php");
    exit();
}

/**
 * Obtém o nome do perfil com base no ID
 * 
 * @param int $perfilId ID do perfil
 * @return string Nome do perfil
 */
function obterNomePerfil($perfilId) {
    $nomes = [
        PERFIL_ADMIN => 'Administrador',
        PERFIL_RH => 'Recursos Humanos',
        PERFIL_COORDENADOR => 'Coordenador',
        PERFIL_COLABORADOR => 'Colaborador',
        PERFIL_CONVIDADO => 'Convidado'
    ];
    
    return $nomes[$perfilId] ?? 'Desconhecido';
}

/**
 * Verifica se o usuário está logado e tem permissão para acessar a página
 */
function verificarAcesso() {
    // Inicia a sessão se ainda não estiver iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Páginas que não requerem autenticação
    $paginas_publicas = [
        'login.php',
        'processa_login.php',
        'recuperar_senha.php',
        'convidado.php',
        'logout.php'
    ];
    
    $pagina_atual = basename($_SERVER['PHP_SELF']);
    
    // Se for uma página pública, não precisa verificar autenticação
    if (in_array($pagina_atual, $paginas_publicas)) {
        return true;
    }
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['utilizador_id']) || !isset($_SESSION['id_perfilacesso'])) {
        redirecionarParaLogin();
    }
    
    // Obtém o perfil do usuário
    $perfilId = $_SESSION['id_perfilacesso'];
    
    // Verifica se o usuário tem permissão para acessar a página
    $caminho = str_replace('/LSIS-Equipa-9/UI/', '', $_SERVER['PHP_SELF']);
    
    if (!verificarPermissao($caminho, $perfilId)) {
        redirecionarAcessoNegado();
    }
    
    return true;
}

// Executa a verificação de acesso em todas as páginas que incluírem este arquivo
// Para desativar em alguma página, defina $pular_verificacao = true antes de incluir
if (!isset($pular_verificacao)) {
    verificarAcesso();
}
?>
