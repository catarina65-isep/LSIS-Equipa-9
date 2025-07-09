<?php
require_once '../DAL/convidado.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Inicializar classe
        $convidado = new Convidado();

        // Sanitizar dados
        $dados = [
            'nome_completo' => filter_input(INPUT_POST, 'nome_completo', FILTER_SANITIZE_STRING),
            'data_nascimento' => filter_input(INPUT_POST, 'data_nascimento'),
            'nif' => filter_input(INPUT_POST, 'nif', FILTER_SANITIZE_STRING),
            'sexo' => filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING),
            'situacao_irs' => filter_input(INPUT_POST, 'situacao_irs', FILTER_SANITIZE_STRING),
            'irs_jovem' => filter_input(INPUT_POST, 'irs_jovem', FILTER_SANITIZE_STRING),
            'niss' => filter_input(INPUT_POST, 'niss', FILTER_SANITIZE_STRING),
            'cc' => filter_input(INPUT_POST, 'cc', FILTER_SANITIZE_STRING),
            'nacionalidade' => filter_input(INPUT_POST, 'nacionalidade', FILTER_SANITIZE_STRING),
            'dependentes' => filter_input(INPUT_POST, 'dependentes', FILTER_SANITIZE_NUMBER_INT),
            'empresa' => filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING),
            'cartao_cidadao' => '',
            'motivo' => filter_input(INPUT_POST, 'motivo', FILTER_SANITIZE_STRING),
            'morada_residencia' => filter_input(INPUT_POST, 'morada_residencia', FILTER_SANITIZE_STRING),
            'localidade' => filter_input(INPUT_POST, 'localidade', FILTER_SANITIZE_STRING),
            'codigo_postal' => filter_input(INPUT_POST, 'codigo_postal', FILTER_SANITIZE_STRING),
            'comprovativo_morada' => '',
            'telemovel' => null,
            'contacto_telefonico' => filter_input(INPUT_POST, 'telemovel', FILTER_SANITIZE_STRING),
            'iban' => filter_input(INPUT_POST, 'iban', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'nome_emergencia' => filter_input(INPUT_POST, 'nome_emergencia', FILTER_SANITIZE_STRING),
            'telefone_emergencia' => filter_input(INPUT_POST, 'telefone_emergencia', FILTER_SANITIZE_STRING),
            'parentesco_emergencia' => filter_input(INPUT_POST, 'parentesco_emergencia', FILTER_SANITIZE_STRING),
            'validade_convite' => null,
            'ativo' => 1,
            'aceite_termos' => 1,
            'matricula' => filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_STRING)
        ];

        // Validar dados
        $erros = $convidado->validarDados($dados);
        if (!empty($erros)) {
            throw new Exception(implode("\n", $erros));
        }

        // Validar telefone
        if (empty($dados['contacto_telefonico'])) {
            $erros[] = 'O telefone é obrigatório';
        } elseif (!preg_match('/^[0-9]{9}$/', $telefone = filter_input(INPUT_POST, 'telemovel', FILTER_SANITIZE_STRING))) {
            $erros[] = 'O telefone deve ter exatamente 9 dígitos';
        }
        $dados['contacto_telefonico'] = $telefone;
        $dados['telemovel'] = $telefone;
        $dados['data_inicio'] = date('Y-m-d H:i:s');

        if (!empty($erros)) {
            throw new Exception(implode("\n", $erros));
        }

        // Criar convidado
        if ($convidado->criarConvidado($dados)) {
            header('Location: obrigado.php');
            exit;
        } else {
            throw new Exception("Erro ao criar convidado");
        }

    } catch (Exception $e) {
        // Em caso de erro, redirecionar com mensagem
        header("Location: convidado.php?erro=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Se não for POST, redirecionar para o formulário
    header('Location: convidado.php');
    exit;
}
