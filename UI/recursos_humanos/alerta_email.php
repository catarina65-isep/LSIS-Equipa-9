<?php
// Função para enviar email de alerta ao colaborador
function enviarAlertaAtualizacao($email, $nome) {
    $assunto = "Atualização de Dados - Recursos Humanos";
    $mensagem = "Olá $nome,\n\nPedimos que atualize os seus dados no sistema. Caso já estejam atualizados, desconsidere este email.\n\nObrigado.";
    $headers = "From: rh@empresa.com\r\nContent-Type: text/plain; charset=UTF-8";
    return mail($email, $assunto, $mensagem, $headers);
}

// Exemplo de uso:
// enviarAlertaAtualizacao('colaborador@empresa.com', 'Nome Colaborador');
