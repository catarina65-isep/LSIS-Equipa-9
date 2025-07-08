<?php
session_start();

require_once __DIR__ . '/../BLL/ColaboradorBLL.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verifica se o perfil é de colaborador
if ($_SESSION['id_perfilacesso'] != 4) {
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
    error_log("Dados recebidos: " . print_r($dados, true));
    error_log("NIF recebido: " . ($dados['nif'] ?? 'null'));

    // Verificar se todos os campos obrigatórios foram preenchidos
    $erros = [];
    if (!$dados['nome']) $erros[] = 'Nome é obrigatório';
    if (!$dados['email']) $erros[] = 'Email é obrigatório';
    if ($dados['nif'] && strlen($dados['nif']) != 9) $erros[] = 'NIF deve ter 9 dígitos';
    if ($dados['telefone'] && !preg_match('/^[0-9]{9}$/', $dados['telefone'])) $erros[] = 'Telefone deve ter 9 dígitos';
    if ($dados['niss'] && !preg_match('/^[0-9]{11}$/', $dados['niss'])) $erros[] = 'NISS deve ter 11 dígitos';
    if ($dados['telemovel_emergencia'] && !preg_match('/^[0-9]{9}$/', $dados['telemovel_emergencia'])) $erros[] = 'Telemóvel deve ter 9 dígitos';
    if ($dados['numero_dependentes'] < 0) $erros[] = 'Número de dependentes deve ser positivo';
    
    if (!empty($erros)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, corrija os seguintes erros:',
            'erros' => $erros
        ]);
        exit;
    }

    // Atualizar os dados do colaborador
    $resultado = $colaboradorBLL->atualizar($dados);
    
    if ($resultado) {
        // Buscar os dados atualizados para confirmar
        $colaborador = $colaboradorBLL->buscarPorId($_SESSION['utilizador_id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Dados atualizados com sucesso',
            'dados_atualizados' => $colaborador
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar dados. Por favor, tente novamente.'
        ]);
        exit;
    }
} catch (Exception $e) {
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
