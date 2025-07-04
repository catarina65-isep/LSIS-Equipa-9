<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Habilita a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define a URL base do sistema
$base_url = '/LSIS-Equipa-9/UI/';

// Função para log
function debug_log($message) {
    $logDir = __DIR__ . '/logs';
    $logFile = $logDir . '/debug.log';
    
    // Cria o diretório de logs se não existir
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    // Tenta escrever no arquivo de log
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    // Tenta escrever no arquivo de log
    $result = @file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    // Se não conseguir escrever no arquivo, tenta escrever em um local alternativo
    if ($result === false) {
        $altLogFile = sys_get_temp_dir() . '/lsis_equipa9_debug.log';
        @file_put_contents($altLogFile, "[Erro ao escrever em $logFile] $logMessage", FILE_APPEND);
    }
}

// Limpa o arquivo de log a cada requisição (comentado para manter o histórico)
// if (file_exists(__DIR__ . '/logs/debug.log')) {
//     unlink(__DIR__ . '/logs/debug.log');
// }

debug_log('=== INÍCIO DO PROCESSAMENTO DO LOGIN ===');
debug_log('Dados recebidos: ' . print_r($_POST, true));

// Inclui os arquivos necessários
require_once __DIR__ . '/../BLL/loginBLL.php';
require_once __DIR__ . '/../DAL/LoginDAL.php';

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = 'Método de requisição inválido.';
    header("Location: {$base_url}login.php");
    exit;
}

try {
    // Limpa mensagens de erro anteriores
    unset($_SESSION['erro']);
    
    // Obtém e valida os dados do formulário
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        throw new Exception('Por favor, preencha todos os campos.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Por favor, insira um email válido.');
    }
    
    // Autentica o usuário
    debug_log('Tentando autenticar usuário: ' . $email);
    $loginBLL = new LoginBLL();
    
    try {
        $usuario = $loginBLL->autenticar($email, $senha);
        debug_log('Resultado da autenticação: ' . print_r($usuario, true));
        
        if (!$usuario) {
            debug_log('Autenticação falhou: credenciais inválidas');
            throw new Exception('Credenciais inválidas. Verifique seu email e senha.');
        }
    } catch (Exception $e) {
        debug_log('Exceção durante autenticação: ' . $e->getMessage());
        throw $e;
    }
    
    // Define as variáveis de sessão
    $_SESSION['utilizador_id'] = $usuario['id_utilizador'];
    $_SESSION['utilizador_email'] = $usuario['email'];
    $_SESSION['utilizador_nome'] = $usuario['username'] ?? 'Utilizador';
    $_SESSION['nome'] = $usuario['username'] ?? 'Utilizador'; // Para compatibilidade
    $_SESSION['id_perfilacesso'] = $usuario['id_perfil_acesso'];
    
    // Obtém o nome do perfil
    $loginDAL = new LoginDAL();
    $_SESSION['perfil_nome'] = $loginDAL->obterNomePerfil($usuario['id_perfil_acesso']);
    
    // Log do perfil do usuário para depuração
    debug_log("ID do perfil do usuário: " . $_SESSION['id_perfilacesso']);
    debug_log("Nome do perfil: " . $_SESSION['perfil_nome']);
    
    // Define a URL de redirecionamento com base no perfil
    if ($_SESSION['id_perfilacesso'] == 2) { // Perfil RH
        $redirect_url = '/LSIS-Equipa-9/UI/recursos_humanos/dashboard.php';
        
        // Verifica se a pasta existe, se não existir, cria
        $rh_dir = __DIR__ . '/recursos_humanos';
        if (!file_exists($rh_dir)) {
            mkdir($rh_dir, 0777, true);
        }
    } else {
        $redirect_url = '/LSIS-Equipa-9/UI/dashboard.php';
    }
    
    // Log para depuração
    debug_log("URL de redirecionamento gerada: $redirect_url");
    
    // Redireciona para a página apropriada
    debug_log("Redirecionando para: " . 'http://' . $_SERVER['HTTP_HOST'] . $redirect_url);
    header("Location: $redirect_url", true, 302);
    exit;
    
} catch (Exception $e) {
    // Log do erro
    $errorMsg = 'Erro no login: ' . $e->getMessage();
    debug_log($errorMsg);
    error_log($errorMsg);
    
    // Define a mensagem de erro na sessão
    $_SESSION['erro'] = $e->getMessage();
    
    // Redireciona de volta para a página de login
    debug_log("Redirecionando para a página de login com erro: " . $e->getMessage());
    header("Location: {$base_url}login.php");
    exit;
}
?>
