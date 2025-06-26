<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';

// Verifica se o usuário está logado e é administrador
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado.']);
    exit;
}

// Obtém o método da requisição
$metodo = $_SERVER['REQUEST_METHOD'];

// Obtém os dados da requisição
$dados = json_decode(file_get_contents('php://input'), true) ?: [];

// Instancia o BLL
$campoBLL = new CampoPersonalizadoBLL();

// Roteamento das requisições
switch ($metodo) {
    case 'GET':
        // Obter um campo específico ou listar todos
        if (isset($_GET['id'])) {
            $resposta = $campoBLL->obterCampoPorId($_GET['id']);
        } else {
            $resposta = $campoBLL->obterCampos();
        }
        break;
        
    case 'POST':
        // Criar um novo campo
        $resposta = $campoBLL->criarCampo($dados);
        if ($resposta['sucesso']) {
            http_response_code(201); // Created
        }
        break;
        
    case 'PUT':
        // Atualizar um campo existente
        if (empty($dados['id'])) {
            http_response_code(400);
            $resposta = ['sucesso' => false, 'erro' => 'ID do campo não informado.'];
        } else {
            $resposta = $campoBLL->atualizarCampo($dados['id'], $dados);
        }
        break;
        
    case 'DELETE':
        // Excluir um campo
        parse_str(file_get_contents('php://input'), $dadosDelete);
        $id = $dadosDelete['id'] ?? null;
        
        if (empty($id)) {
            http_response_code(400);
            $resposta = ['sucesso' => false, 'erro' => 'ID do campo não informado.'];
        } else {
            $resposta = $campoBLL->excluirCampo($id);
            if ($resposta['sucesso']) {
                http_response_code(204); // No Content
                exit;
            }
        }
        break;
        
    default:
        http_response_code(405); // Method Not Allowed
        $resposta = ['sucesso' => false, 'erro' => 'Método não permitido.'];
        break;
}

// Retorna a resposta como JSON
echo json_encode($resposta);
?>
