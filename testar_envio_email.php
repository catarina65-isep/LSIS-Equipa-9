<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir o arquivo de configuração do PHPMailer
require_once 'DAL/PHPMailerConfig.php';

// Email de teste
$para = 'catarina.cardoso@tlantic.pt'; // Substitua pelo email de teste
$assunto = 'Teste de Envio de E-mail';
$mensagem = '<h1>Teste de Envio</h1>'
          . '<p>Este é um e-mail de teste enviado pelo sistema.</p>'
          . '<p>Se você está recebendo esta mensagem, o envio de e-mails está funcionando corretamente!</p>';

echo "<h1>Teste de Envio de E-mail</h1>";
echo "<p>Enviando e-mail para: $para</p>";

try {
    // Tentar enviar o e-mail
    $enviado = sendEmail($para, $assunto, $mensagem);
    
    if ($enviado) {
        echo "<p style='color:green;'>E-mail enviado com sucesso!</p>";
    } else {
        echo "<p style='color:red;'>Falha ao enviar o e-mail. Verifique os logs para mais informações.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Mostrar o conteúdo do log de erros
$logFile = dirname(__FILE__) . '/logs/email_errors.log';
if (file_exists($logFile)) {
    echo "<h2>Log de Erros:</h2>";
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
} else {
    echo "<p>Nenhum log de erro encontrado em: " . htmlspecialchars($logFile) . "</p>";
}

// Mostrar o log de erros do PHP
$phpLogFile = '/Applications/MAMP/logs/php_error.log';
if (file_exists($phpLogFile)) {
    echo "<h2>Log do PHP:</h2>";
    $phpLog = `tail -n 50 "$phpLogFile"`;
    echo "<pre>" . htmlspecialchars($phpLog) . "</pre>";
}
?>
