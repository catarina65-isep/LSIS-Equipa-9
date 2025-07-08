<?php
session_start();

// Configurar log
$logFile = __DIR__ . '/../../php_error_log.txt';

function logDebug($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

require_once __DIR__ . '/../BLL/ColaboradorBLL.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    logDebug("Erro: Usuário não logado");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verifica se o perfil é de colaborador
if ($_SESSION['id_perfilacesso'] != 4) {
    logDebug("Erro: Acesso não autorizado");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

try {
    $colaboradorBLL = new ColaboradorBLL();
    
    // Obter os dados do formulário
    $dados = [
        'id_utilizador' => $_SESSION['utilizador_id'],
        'nome' => $_POST['nome'] ?? null,
        'email' => $_POST['email'] ?? null,
        'telefone' => $_POST['telefone'] ?? null,
        'nif' => $_POST['nif'] ?? null,
        'morada' => $_POST['morada'] ?? null,
        'data_nascimento' => $_POST['dataNascimento'] ?? null,
        'genero' => $_POST['genero'] ?? null,
        'estado_civil' => $_POST['estadoCivil'] ?? null,
        'niss' => $_POST['niss'] ?? null,
        'numero_dependentes' => $_POST['numeroDependentes'] ?? 0,
        'habilitacoes' => $_POST['habilitacoes'] ?? null,
        'contacto_emergencia' => $_POST['contactoEmergencia'] ?? null,
        'relacao_emergencia' => $_POST['relacaoEmergencia'] ?? null,
        'telemovel_emergencia' => $_POST['telemovelEmergencia'] ?? null
    ];

    // Log dos dados recebidos
    logDebug("Dados recebidos: " . print_r($dados, true));

    // Verificar se todos os campos obrigatórios foram preenchidos
    if (!$dados['nome'] || !$dados['email']) {
        logDebug("Erro: Campos obrigatórios não preenchidos");
        throw new Exception("Por favor, preencha todos os campos obrigatórios");
    }

    // Atualizar os dados do colaborador
    $resultado = $colaboradorBLL->atualizar($dados);
    
    if ($resultado) {
        // Buscar os dados atualizados para confirmar
        $colaborador = $colaboradorBLL->buscarPorId($_SESSION['utilizador_id']);
        logDebug("Dados após atualização: " . print_r($colaborador, true));
    } else {
        logDebug("Erro: Atualização falhou");
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $resultado,
        'message' => $resultado ? 'Dados atualizados com sucesso' : 'Erro ao atualizar dados',
        'debug' => [
            'dados_recebidos' => $dados,
            'resultado' => $resultado
        ]
    ]);
    exit;
} catch (Exception $e) {
    // Log do erro
    logDebug("Erro: " . $e->getMessage());
    logDebug("Stack trace: " . $e->getTraceAsString());
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar os dados: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
    exit;
}
