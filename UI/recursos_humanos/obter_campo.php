<?php
session_start();

// Verifica se o utilizador está logado e tem permissão (RH ou Administrador)
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']);
    exit;
}

require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';

// Verificar se o ID do campo foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['sucesso' => false, 'erro' => 'ID do campo não fornecido ou inválido']);
    exit;
}

$campoBLL = new CampoPersonalizadoBLL();
$resultado = $campoBLL->obterCampoPorId($_GET['id']);

// Verificar se o campo foi encontrado
if (!$resultado['sucesso']) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['sucesso' => false, 'erro' => 'Campo não encontrado']);
    exit;
}

// Retornar os dados do campo
header('Content-Type: application/json');
echo json_encode($resultado);
?>
