<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Verifica se os campos obrigatórios foram enviados
if (empty($_POST['nome'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'O nome do perfil é obrigatório.']);
    exit;
}

// Inclui as classes necessárias
require_once __DIR__ . '/../../DAL/PerfilAcessoDAL.php';
require_once __DIR__ . '/../../BLL/PerfilAcessoBLL.php';

// Configuração para retornar JSON
header('Content-Type: application/json');

try {
    // Obtém os dados do formulário
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $nome = trim($_POST['nome']);
    $descricao = !empty($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $icone = !empty($_POST['icone']) ? trim($_POST['icone']) : 'user';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $acao = isset($_POST['acao']) ? $_POST['acao'] : 'criar';
    
    // Cria um array com os dados do perfil
    $dadosPerfil = [
        'nome_perfil' => $nome,
        'descricao' => $descricao,
        'nivel_acesso' => 1, // Nível de acesso padrão
        'permissoes' => '{}', // Permissões vazias por padrão
        'ativo' => $ativo,
        'icone' => $icone
    ];
    
    $perfilDAL = new PerfilAcessoDAL();
    $perfilBLL = new PerfilAcessoBLL();
    
    if ($acao === 'atualizar' && $id) {
        // Atualização de perfil existente
        $perfilExistente = $perfilDAL->obterPorId($id);
        
        if (!$perfilExistente) {
            throw new Exception('Perfil não encontrado.');
        }
        
        // Verifica se já existe outro perfil com o mesmo nome
        $perfilComMesmoNome = $perfilDAL->buscarPorNome($nome);
        if ($perfilComMesmoNome && $perfilComMesmoNome['id_perfilacesso'] != $id) {
            echo json_encode([
                'success' => false,
                'message' => 'Já existe um perfil com este nome.'
            ]);
            exit;
        }
        
        // Atualiza o perfil
        $resultado = $perfilDAL->atualizar($id, $dadosPerfil);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!',
                'id' => $id
            ]);
        } else {
            throw new Exception('Não foi possível atualizar o perfil.');
        }
    } else {
        // Criação de novo perfil
        // Verifica se já existe um perfil com o mesmo nome
        $perfilExistente = $perfilDAL->buscarPorNome($nome);
        
        if ($perfilExistente) {
            echo json_encode([
                'success' => false,
                'message' => 'Já existe um perfil com este nome.'
            ]);
            exit;
        }
        
        // Insere o novo perfil
        $resultado = $perfilDAL->inserir($dadosPerfil);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Perfil criado com sucesso!',
                'id' => $resultado
            ]);
        } else {
            throw new Exception('Não foi possível criar o perfil.');
        }
    }
    
} catch (Exception $e) {
    error_log('Erro ao processar perfil: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
