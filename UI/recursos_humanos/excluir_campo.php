<?php
session_start();

// Log para depuração
error_log('Tentativa de exclusão de campo personalizado - Dados da sessão: ' . print_r($_SESSION, true));

// Verifica se o usuário está logado e tem permissão (RH ou Admin)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    error_log('Acesso negado: permissão insuficiente');
    $_SESSION['mensagem'] = 'Acesso negado. Permissão insuficiente.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: campos_personalizados.php');
    exit;
}

// Verifica se o ID do campo foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = 'ID do campo inválido.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: campos_personalizados.php');
    exit;
}

$campoId = (int)$_GET['id'];

// Inclui as classes necessárias
require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';

// Obtém a instância do BLL usando o padrão Singleton
$campoBLL = CampoPersonalizadoBLL::getInstance();

try {
    // Tenta excluir o campo
    $resultado = $campoBLL->excluirCampo($campoId);
    
    if ($resultado['success']) {
        $_SESSION['mensagem'] = $resultado['message'];
        $_SESSION['tipo_mensagem'] = 'success';
    } else {
        $_SESSION['mensagem'] = $resultado['erro'] ?? 'Erro ao excluir o campo.';
        $_SESSION['tipo_mensagem'] = 'danger';
    }
} catch (Exception $e) {
    error_log('Erro ao excluir campo: ' . $e->getMessage());
    $_SESSION['mensagem'] = 'Erro ao excluir o campo: ' . $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

// Redireciona de volta para a página de listagem
header('Location: campos_personalizados.php');
exit;
