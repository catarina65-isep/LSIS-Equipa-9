<?php
session_start();

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit;
}

require_once __DIR__ . '/../../BLL/PerfilAcessoBLL.php';
require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';

header('Content-Type: application/json');

try {
    $perfilBLL = new PerfilAcessoBLL();
    
    // Obter lista de perfis
    $perfis = $perfilBLL->listarTodos();
    
    // Contar perfis ativos
    $totalAtivos = count(array_filter($perfis, function($p) {
        return $p['ativo'] == 1;
    }));
    
    // Retornar os dados
    echo json_encode([
        'success' => true,
        'totalPerfis' => count($perfis),
        'totalAtivos' => $totalAtivos
    ]);
    
} catch (Exception $e) {
    error_log('Erro em obter_estatisticas_perfis.php: ' . $e->getMessage());
    
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => 'Ocorreu um erro ao processar a requisição.'
    ]);
}
