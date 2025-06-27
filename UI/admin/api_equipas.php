<?php
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../includes/verificar_acesso.php';

header('Content-Type: application/json');

$equipaBLL = new EquipaBLL();
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

try {
    switch ($acao) {
        case 'obter':
            if (!isset($_GET['id'])) {
                throw new Exception('ID da equipe não informado');
            }
            $equipa = $equipaBLL->obterEquipa($_GET['id']);
            echo json_encode(['sucesso' => true, 'dados' => $equipa]);
            break;
            
        case 'obter_membros':
            if (!isset($_GET['equipa_id'])) {
                throw new Exception('ID da equipe não informado');
            }
            $membros = $equipaBLL->obterMembrosEquipa($_GET['equipa_id']);
            echo json_encode(['sucesso' => true, 'dados' => $membros]);
            break;
            
        case 'listar':
            $equipas = $equipaBLL->listarEquipas();
            echo json_encode(['sucesso' => true, 'dados' => $equipas]);
            break;
            
        case 'criar':
            if ($metodo !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $dados = json_decode(file_get_contents('php://input'), true);
            if (empty($dados['nome']) || empty($dados['coordenador_id'])) {
                throw new Exception('Dados incompletos');
            }
            
            $id = $equipaBLL->criarEquipa($dados);
            
            // Adiciona membros se fornecidos
            if (!empty($dados['membros']) && is_array($dados['membros'])) {
                foreach ($dados['membros'] as $membroId) {
                    if ($membroId != $dados['coordenador_id']) {
                        $equipaBLL->adicionarMembro($id, $membroId);
                    }
                }
            }
            
            echo json_encode(['sucesso' => true, 'id' => $id]);
            break;
            
        case 'atualizar':
            if ($metodo !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $dados = json_decode(file_get_contents('php://input'), true);
            if (empty($dados['id']) || empty($dados['nome']) || empty($dados['coordenador_id'])) {
                throw new Exception('Dados incompletos');
            }
            
            $equipaBLL->atualizarEquipa($dados['id'], $dados);
            
            // Atualiza membros se fornecidos
            if (isset($dados['membros']) && is_array($dados['membros'])) {
                // Primeiro, remove todos os membros (exceto o coordenador)
                $membrosAtuais = $equipaBLL->obterMembrosEquipa($dados['id']);
                foreach ($membrosAtuais as $membro) {
                    if ($membro['id'] != $dados['coordenador_id']) {
                        $equipaBLL->removerMembro($dados['id'], $membro['id']);
                    }
                }
                
                // Depois, adiciona os novos membros
                foreach ($dados['membros'] as $membroId) {
                    if ($membroId != $dados['coordenador_id']) {
                        $equipaBLL->adicionarMembro($dados['id'], $membroId);
                    }
                }
                
                // Garante que o coordenador está na equipe
                $equipaBLL->adicionarMembro($dados['id'], $dados['coordenador_id']);
            }
            
            echo json_encode(['sucesso' => true]);
            break;
            
        case 'excluir':
            if ($metodo !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $dados = json_decode(file_get_contents('php://input'), true);
            if (empty($dados['id'])) {
                throw new Exception('ID da equipe não informado');
            }
            
            $equipaBLL->excluirEquipa($dados['id']);
            echo json_encode(['sucesso' => true]);
            break;
            
        default:
            throw new Exception('Ação não reconhecida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
