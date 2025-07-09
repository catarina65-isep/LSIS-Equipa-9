<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é um coordenador
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 3) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit;
}

// Inclui as classes necessárias
require_once __DIR__ . '/../../BLL/CoordenadorBLL.php';

// Configura o cabeçalho para retornar JSON
header('Content-Type: application/json');

// Verifica se os dados necessários foram fornecidos
if (!isset($_POST['id_colaborador']) || !isset($_POST['id_equipa'])) {
    echo json_encode(['success' => false, 'message' => 'Dados insuficientes para a operação.']);
    exit;
}

$idColaborador = intval($_POST['id_colaborador']);
$idEquipa = intval($_POST['id_equipa']);

// Valida os IDs
if ($idColaborador <= 0 || $idEquipa <= 0) {
    echo json_encode(['success' => false, 'message' => 'IDs inválidos fornecidos.']);
    exit;
}

try {
    // Cria uma instância do BLL e chama o método para remover o membro
    $coordenadorBLL = new CoordenadorBLL();
    $resultado = $coordenadorBLL->removerMembroEquipe($idColaborador, $idEquipa);
    
    // Retorna o resultado da operação
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log('Erro ao remover membro da equipe: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.'
    ]);
}
