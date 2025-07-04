<?php
// Caminho para os arquivos do PHPMailer baixados
$phpmailer_path = dirname(__DIR__) . '/vendor/PHPMailer-6.8.1/src';

// Incluir os arquivos necessários do PHPMailer
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    require_once $phpmailer_path . '/Exception.php';
    require_once $phpmailer_path . '/PHPMailer.php';
    require_once $phpmailer_path . '/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $isHTML = true) {
    $mail = new PHPMailer(true);
    
    // Configurações de depuração
    $mail->SMTPDebug = 2; // Ativa saída de depuração detalhada
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer: $str");
        echo "PHPMailer: $str<br>\n";
    };

    try {
        // Verificar se o arquivo de log está acessível
        $logFile = dirname(__DIR__) . '/logs/email_errors.log';
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        // Configurações do servidor SMTP do Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tlantic901@gmail.com';
        $mail->Password = 'epwqkaifbwzwxnuy';  // Senha de aplicativo gerada
        $mail->SMTPSecure = 'tls';  // Usar TLS
        $mail->Port = 587;
        
        // Configurações adicionais
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Timeout = 30;  // Tempo limite de conexão em segundos

        // Remetente e destinatário
        $mail->setFrom('tlantic901@gmail.com', 'Sistema de Convites - Tlantic');
        $mail->addAddress($to);

        // Conteúdo
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Se for HTML, adiciona versão em texto puro
        if ($isHTML) {
            $mail->AltBody = strip_tags($body);
        }

        // Configurações de segurança
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Tenta enviar o e-mail
        $enviado = $mail->send();
        
        // Log do resultado
        $logMessage = date('Y-m-d H:i:s') . " - E-mail para $to - " . 
                     ($enviado ? 'Enviado com sucesso' : 'Falha no envio') . "\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        return $enviado;
        
    } catch (Exception $e) {
        $errorMsg = "Erro ao enviar e-mail para $to: " . $e->getMessage();
        if (isset($mail->ErrorInfo)) {
            $errorMsg .= " - " . $mail->ErrorInfo;
        }
        
        // Log detalhado do erro
        $logFile = dirname(__DIR__) . '/logs/email_errors.log';
        $logMessage = date('Y-m-d H:i:s') . " - ERRO: $errorMsg\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
}
