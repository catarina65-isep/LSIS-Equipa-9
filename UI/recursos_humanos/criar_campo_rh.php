<?php
session_start();

// Verificar se o usuário está logado e tem permissão (RH ou Administrador)
if (!isset($_SESSION['utilizador_id'])) {
    $_SESSION['mensagem'] = 'Usuário não autenticado.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: login.php');
    exit();
}

// Verificar perfil de acesso
if (!in_array($_SESSION['id_perfilacesso'] ?? 0, [1, 2])) {
    $_SESSION['mensagem'] = 'Acesso não autorizado. Apenas RH e Administradores podem acessar esta página.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: index.php');
    exit();
}

// Incluir as classes necessárias
require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';


try {
    $campoBLL = CampoPersonalizadoBLL::getInstance();
    
    // Verificar se o campo já existe
    $campos = $campoBLL->obterCampos();
    $campoExiste = false;
    
    if (isset($campos['dados'])) {
        foreach ($campos['dados'] as $campo) {
            if (isset($campo['nome']) && $campo['nome'] === 'disponivel_rh') {
                $campoExiste = true;
                break;
            }
        }
    }
    
    if ($campoExiste) {
        echo "O campo 'disponivel_rh' já existe no sistema.";
        exit();
    }
    
    // Dados do campo personalizado para RH
    // Usando apenas os campos que existem na tabela campo_personalizado
    $dadosCampo = [
        'nome' => 'disponivel_rh',
        'rotulo' => 'Disponível para RH',
        'tipo' => 'texto',
        'descricao' => 'Disponível para o RH, podem personalizar um campo (descrição, tipo, permissão, obrigatoriedade, necessidade de comprovativo)',
        'categoria' => 'outros',
        'obrigatorio' => 0,
        'ativo' => 1,
        'dica' => 'Este campo pode ser personalizado pelo RH conforme necessário.',
        'visivel' => 1,
        'editavel' => 1
    ];
    
    // Adicionar campos opcionais que podem existir na tabela
    if (isset($_SESSION['utilizador_id'])) {
        $dadosCampo['id_utilizador_criacao'] = $_SESSION['utilizador_id'];
        $dadosCampo['id_utilizador_atualizacao'] = $_SESSION['utilizador_id'];
    }
    
    // Log dos dados que serão enviados para criar o campo
    error_log('Dados que serão enviados para criarCampo: ' . print_r($dadosCampo, true));
    
    // Criar o campo
    error_log('=== INÍCIO DA CRIAÇÃO DO CAMPO ===');
    error_log('Dados enviados para criarCampo: ' . print_r($dadosCampo, true));
    
    try {
        $resultado = $campoBLL->criarCampo($dadosCampo);
        error_log('Resultado retornado por criarCampo: ' . print_r($resultado, true));
        
        if (isset($resultado['sucesso']) && $resultado['sucesso']) {
            $mensagem = "Campo 'Disponível para RH' criado com sucesso! ID: " . ($resultado['id'] ?? 'N/A');
            error_log($mensagem);
            echo $mensagem;
        } else {
            $erro = "Erro ao criar o campo: ";
            if (is_array($resultado['erro'] ?? '')) {
                $erro .= implode(" ", $resultado['erro']);
            } else {
                $erro .= ($resultado['erro'] ?? 'Erro desconhecido ao criar campo.');
            }
            error_log($erro);
            echo $erro;
        }
    } catch (Exception $e) {
        $erro = "Exceção ao criar campo: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        error_log($erro);
        echo $erro;
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
