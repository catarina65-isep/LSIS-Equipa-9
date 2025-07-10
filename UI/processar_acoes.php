<?php
session_start();
require_once '../DAL/convidado.php';

if (isset($_GET['action'])) {
    try {
        $convidado = new Convidado();
        $id = $_GET['id'];
        
        switch ($_GET['action']) {
            case 'aceitar':
                $convidado->aceitar($id);
                $mensagem = 'Candidato aceito com sucesso';
                break;
            case 'rejeitar':
                $convidado->rejeitar($id);
                $mensagem = 'Candidato rejeitado com sucesso';
                break;
            case 'excluir':
                $convidado->excluir($id);
                $mensagem = 'Candidato excluído com sucesso';
                break;
            default:
                throw new Exception('Ação inválida');
        }
        
        $_SESSION['mensagem'] = $mensagem;
        header('Location: recursos_humanos/gerir_fichas.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: recursos_humanos/gerir_fichas.php');
        exit;
    }
}

// Redirecionar se não houver ação válida
header('Location: recursos_humanos/gerir_fichas.php');
exit;
