<?php
session_start();

// Verificar se o usuário está logado e tem permissão (RH ou Administrador)
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    $_SESSION['mensagem'] = 'Acesso não autorizado.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: login.php');
    exit();
}

// Incluir as classes necessárias
require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';

// Inicializar a BLL
$campoBLL = CampoPersonalizadoBLL::getInstance();

// Processar a ação solicitada
$acao = $_POST['acao'] ?? '';
$erro = '';
$sucesso = false;

try {
    switch ($acao) {
        case 'criar':
        case 'editar':
            // Preparar os dados do formulário
            $dados = [
                'nome' => trim($_POST['nome'] ?? ''),
                'tipo' => $_POST['tipo'] ?? '',
                'rotulo' => trim($_POST['rotulo'] ?? ''),
                'categoria' => trim($_POST['categoria'] ?? ''),
                'opcoes' => trim($_POST['opcoes'] ?? ''),
                'obrigatorio' => isset($_POST['obrigatorio']) ? 1 : 0,
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'requer_comprovativo' => isset($_POST['requer_comprovativo']) ? 1 : 0,
                'visivel_listagem' => 1,
                'validacao' => '',
                'ordem' => 0,
                'grupo' => '',
                'descricao' => trim($_POST['descricao'] ?? '')
            ];
            
            // Processar visível para
            if (isset($_POST['visivel_para']) && is_array($_POST['visivel_para'])) {
                $dados['visivel_para'] = $_POST['visivel_para'];
            } else {
                $dados['visivel_para'] = [];
            }

            // Se for uma nova categoria, usar o valor do campo de texto
            if ($dados['categoria'] === 'outro' && !empty(trim($_POST['novaCategoria'] ?? ''))) {
                $dados['categoria'] = trim($_POST['novaCategoria']);
            }

            // Validar dados obrigatórios
            $erros = [];
            
            if (empty($dados['nome'])) {
                $erros[] = 'O nome do campo é obrigatório.';
            } elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $dados['nome'])) {
                $erros[] = 'O nome do campo deve conter apenas letras minúsculas, números e underscore, e deve começar com uma letra.';
            }
            
            if (empty($dados['tipo'])) {
                $erros[] = 'O tipo do campo é obrigatório.';
            }
            
            if (empty($dados['rotulo'])) {
                $erros[] = 'O rótulo do campo é obrigatório.';
            }
            
            if (empty($dados['categoria'])) {
                $erros[] = 'A categoria do campo é obrigatória.';
            }
            
            // Validar opções para tipos que as requerem
            $tiposComOpcoes = ['selecao', 'multiselecao', 'radio', 'checkbox', 'sim_nao'];
            if (in_array($dados['tipo'], $tiposComOpcoes) && empty($dados['opcoes'])) {
                $erros[] = 'É necessário informar as opções para este tipo de campo.';
            }

            if (!empty($erros)) {
                throw new Exception(implode('<br>', $erros));
            }

            if ($acao === 'criar') {
                // Criar novo campo
                error_log('=== INÍCIO DA CRIAÇÃO DE CAMPO ===');
                error_log('Dados recebidos do formulário: ' . print_r($dados, true));
                
                try {
                    error_log('Chamando criarCampo com dados: ' . print_r($dados, true));
                    $resultado = $campoBLL->criarCampo($dados);
                    error_log('Resultado da criação: ' . print_r($resultado, true));
                    
                    if (isset($resultado['sucesso']) && $resultado['sucesso'] === true) {
                        // Define a mensagem de sucesso na sessão
                        $_SESSION['mensagem'] = $resultado['mensagem'] ?? 'Campo criado com sucesso!';
                        $_SESSION['tipo_mensagem'] = 'success';
                        
                        // Retorna sucesso para o AJAX
                        echo json_encode([
                            'success' => true,
                            'redirect' => 'campos_personalizados.php'
                        ]);
                        exit();
                    } else {
                        // Se houver erros, lança uma exceção com a mensagem de erro
                        $erro = $resultado['erro'] ?? 'Erro desconhecido ao criar campo.';
                        if (is_array($erro)) {
                            $erro = implode(' ', $erro);
                        }
                        throw new Exception($erro);
                    }
                } catch (Exception $e) {
                    error_log('Erro ao criar campo: ' . $e->getMessage());
                    error_log('Stack trace: ' . $e->getTraceAsString());
                    throw $e;
                }
            } else {
                // Atualizar campo existente
                $campoId = $_POST['campoId'] ?? 0;
                if (empty($campoId) || !is_numeric($campoId)) {
                    throw new Exception('ID do campo inválido para edição.');
                }
                
                $resultado = $campoBLL->atualizarCampo($campoId, $dados);
                $mensagem = 'Campo atualizado com sucesso!';
            }
            
            $_SESSION['mensagem'] = $mensagem;
            $_SESSION['tipo_mensagem'] = 'success';
            break;
            
        // A exclusão agora é tratada no arquivo excluir_campo.php
        
        default:
            throw new Exception('Ação inválida.');
    }
    
} catch (Exception $e) {
    // Em caso de erro, retorna JSON com a mensagem de erro
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'erro' => $e->getMessage()
    ]);
    exit();
}

// Se chegar até aqui, algo deu errado
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'erro' => $erro ?: 'Ocorreu um erro ao processar sua solicitação.'
]);
exit();
