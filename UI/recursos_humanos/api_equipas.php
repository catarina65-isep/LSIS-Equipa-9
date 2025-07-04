<?php
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../includes/verificar_acesso.php';

// Ativar exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configurar cabeçalhos CORS se necessário
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Log da requisição recebida
error_log('Requisição recebida: ' . print_r([
    'method' => $_SERVER['REQUEST_METHOD'],
    'query' => $_GET,
    'post' => $_POST,
    'input' => file_get_contents('php://input')
], true));

$equipaBLL = new EquipaBLL();
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

try {
    switch ($acao) {
        case 'obter':
            if (!isset($_GET['id'])) {
                throw new Exception('ID da equipa não informado');
            }
            $equipa = $equipaBLL->obterEquipa($_GET['id']);
            echo json_encode(['sucesso' => true, 'dados' => $equipa]);
            break;
            
        case 'obter_membros':
            if (!isset($_GET['equipa_id'])) {
                throw new Exception('ID da equipa não informado');
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
            
            try {
                // Log dos headers recebidos
                error_log('Headers recebidos: ' . print_r(getallheaders(), true));
                
                // Obter o conteúdo bruto da requisição
                $input = file_get_contents('php://input');
                error_log('Conteúdo bruto recebido: ' . $input);
                
                // Decodificar o JSON
                $dados = json_decode($input, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
                }
                
                // Log dos dados recebidos
                error_log('Dados recebidos para criar equipe: ' . print_r($dados, true));
                
                if (empty($dados['nome']) || empty($dados['coordenador_id'])) {
                    $erro = 'Dados incompletos. ';
                    $erro .= 'Nome: ' . (empty($dados['nome']) ? 'faltando' : 'presente') . ', ';
                    $erro .= 'Coordenador ID: ' . (empty($dados['coordenador_id']) ? 'faltando' : 'presente');
                    throw new Exception($erro);
                }
                
                error_log('Chamando criarEquipa com os seguintes dados: ' . print_r([
                    'nome' => $dados['nome'],
                    'descricao' => $dados['descricao'] ?? '',
                    'coordenador_id' => $dados['coordenador_id']
                ], true));
                
                // Criar a equipe
                $id = $equipaBLL->criarEquipa([
                    'nome' => $dados['nome'],
                    'descricao' => $dados['descricao'] ?? '',
                    'coordenador_id' => $dados['coordenador_id']
                ]);
                
                error_log('Equipa criada com sucesso. ID: ' . $id);
                
                // Adiciona membros se fornecidos
                if (!empty($dados['membros']) && is_array($dados['membros'])) {
                    error_log('Adicionando membros à equipe: ' . print_r($dados['membros'], true));
                    foreach ($dados['membros'] as $membroId) {
                        if ($membroId != $dados['coordenador_id']) {
                            $equipaBLL->adicionarMembro($id, $membroId);
                            error_log("Membro $membroId adicionado à equipe $id");
                        }
                    }
                }
                
                $response = [
                    'success' => true, 
                    'message' => 'Equipa criada com sucesso!',
                    'id' => $id
                ];
                
                error_log('Resposta enviada: ' . print_r($response, true));
                echo json_encode($response);
                
            } catch (Exception $e) {
                $errorMessage = 'Erro ao criar equipe: ' . $e->getMessage();
                error_log($errorMessage);
                error_log('Stack trace: ' . $e->getTraceAsString());
                
                http_response_code(400);
                $response = [
                    'success' => false,
                    'message' => $errorMessage,
                    'trace' => $e->getTraceAsString()
                ];
                
                error_log('Erro enviado para o cliente: ' . print_r($response, true));
                echo json_encode($response);
            }
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
                
                // Garante que o coordenador está na equipa
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
                throw new Exception('ID da equipa não informado');
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
