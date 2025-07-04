<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado e tem permissão de RH ou Admin
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => false, 'erro' => 'Não autorizado.']);
    exit();
}

// Incluir o autoloader
require_once __DIR__ . '/../../autoload.php';

// Configurar o cabeçalho para retorno JSON
header('Content-Type: application/json');

// Verificar se há uma ação definida
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$equipaId = $_POST['id'] ?? $_GET['id'] ?? 0;

// Inicializar a resposta
$resposta = ['sucesso' => false, 'mensagem' => ''];

try {
    // Inicializar o BLL
    $equipaBLL = new EquipaBLL();
    $utilizadorBLL = new UtilizadorBLL();

    switch ($acao) {
        case 'criar':
            // Validar dados recebidos
            $dados = [
                'nome' => trim($_POST['nome'] ?? ''),
                'descricao' => trim($_POST['descricao'] ?? ''),
                'coordenador_id' => (int)($_POST['coordenador_id'] ?? 0),
                'id_departamento' => !empty($_POST['id_departamento']) ? (int)$_POST['id_departamento'] : null,
                'id_equipa_pai' => !empty($_POST['id_equipa_pai']) ? (int)$_POST['id_equipa_pai'] : null
            ];

            // Validar dados obrigatórios
            if (empty($dados['nome'])) {
                throw new Exception('O nome da equipa é obrigatório.');
            }

            if (empty($dados['coordenador_id'])) {
                throw new Exception('O coordenador da equipa é obrigatório.');
            }

            // Criar a equipa
            $equipaId = $equipaBLL->criarEquipa($dados);
            
            // Adicionar membros à equipa, se informados
            if (!empty($_POST['membros']) && is_array($_POST['membros'])) {
                foreach ($_POST['membros'] as $membroId) {
                    if ($membroId != $dados['coordenador_id']) { // Não adicionar o coordenador como membro
                        $equipaBLL->adicionarMembro($equipaId, (int)$membroId);
                    }
                }
            }

            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Equipa criada com sucesso!',
                'id' => $equipaId
            ];
            break;

        case 'editar':
            // Validar ID da equipa
            if (empty($equipaId)) {
                throw new Exception('ID da equipa não informado.');
            }

            // Verificar se a equipa existe
            $equipa = $equipaBLL->obterEquipa($equipaId);
            if (!$equipa) {
                throw new Exception('Equipa não encontrada.');
            }

            // Validar dados recebidos
            $dados = [
                'nome' => trim($_POST['nome'] ?? $equipa['nome']),
                'descricao' => trim($_POST['descricao'] ?? $equipa['descricao']),
                'coordenador_id' => !empty($_POST['coordenador_id']) ? (int)$_POST['coordenador_id'] : $equipa['id_coordenador'],
                'id_departamento' => isset($_POST['id_departamento']) ? (int)$_POST['id_departamento'] : $equipa['id_departamento'],
                'id_equipa_pai' => isset($_POST['id_equipa_pai']) ? (int)$_POST['id_equipa_pai'] : $equipa['id_equipa_pai']
            ];

            // Validar dados obrigatórios
            if (empty($dados['nome'])) {
                throw new Exception('O nome da equipa é obrigatório.');
            }

            if (empty($dados['coordenador_id'])) {
                throw new Exception('O coordenador da equipa é obrigatório.');
            }

            // Atualizar a equipa
            $equipaBLL->atualizarEquipa($equipaId, $dados);

            // Atualizar membros da equipa, se informados
            if (isset($_POST['membros']) && is_array($_POST['membros'])) {
                $membros = array_map('intval', $_POST['membros']);
                // Garantir que o coordenador esteja na lista de membros
                if (!in_array($dados['coordenador_id'], $membros)) {
                    $membros[] = $dados['coordenador_id'];
                }
                $equipaBLL->atualizarMembrosEquipa($equipaId, $membros);
            }

            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Equipa atualizada com sucesso!'
            ];
            break;

        case 'excluir':
            // Validar ID da equipa
            if (empty($equipaId)) {
                throw new Exception('ID da equipa não informado.');
            }

            // Excluir a equipa
            $resultado = $equipaBLL->excluirEquipa($equipaId);
            
            if (!$resultado) {
                throw new Exception('Não foi possível excluir a equipa. Verifique se existem dependências.');
            }

            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Equipa excluída com sucesso!'
            ];
            break;

        case 'obter':
            // Validar ID da equipa
            if (empty($equipaId)) {
                throw new Exception('ID da equipa não informado.');
            }

            // Obter dados da equipa
            $equipa = $equipaBLL->obterEquipa($equipaId);
            if (!$equipa) {
                throw new Exception('Equipa não encontrada.');
            }

            // Obter membros da equipa
            $membros = $equipaBLL->obterMembrosEquipa($equipaId);
            
            $resposta = [
                'sucesso' => true,
                'dados' => [
                    'equipa' => $equipa,
                    'membros' => $membros
                ]
            ];
            break;

        case 'listar':
            // Obter lista de equipes
            $equipas = $equipaBLL->listarEquipas();
            
            $resposta = [
                'sucesso' => true,
                'dados' => $equipas
            ];
            break;

        case 'adicionar_membro':
            // Validar IDs
            $membroId = (int)($_POST['membro_id'] ?? 0);
            if (empty($equipaId) || empty($membroId)) {
                throw new Exception('ID da equipa e do membro são obrigatórios.');
            }

            // Adicionar membro à equipa
            $equipaBLL->adicionarMembro($equipaId, $membroId);
            
            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Membro adicionado com sucesso!'
            ];
            break;

        case 'remover_membro':
            // Validar IDs
            $membroId = (int)($_POST['membro_id'] ?? 0);
            if (empty($equipaId) || empty($membroId)) {
                throw new Exception('ID da equipa e do membro são obrigatórios.');
            }

            // Remover membro da equipa
            $equipaBLL->removerMembro($equipaId, $membroId);
            
            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Membro removido com sucesso!'
            ];
            break;

        case 'definir_coordenador':
            // Validar IDs
            $membroId = (int)($_POST['membro_id'] ?? 0);
            if (empty($equipaId) || empty($membroId)) {
                throw new Exception('ID da equipa e do membro são obrigatórios.');
            }

            // Definir coordenador da equipa
            $equipaBLL->definirCoordenador($equipaId, $membroId);
            
            $resposta = [
                'sucesso' => true,
                'mensagem' => 'Coordenador definido com sucesso!'
            ];
            break;

        default:
            throw new Exception('Ação não reconhecida.');
    }

} catch (Exception $e) {
    $resposta = [
        'sucesso' => false,
        'erro' => $e->getMessage()
    ];
}

// Retornar a resposta em formato JSON
echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
