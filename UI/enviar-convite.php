<?php
session_start();
require_once __DIR__ . '/../DAL/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um endereço de e-mail válido.';
    } else {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        try {
            $pdo = Database::getInstance();
            
            // Check if email already has a pending token
            $stmt = $pdo->prepare("SELECT * FROM convite_convites WHERE email = ? AND usado_em IS NULL AND expira_em > NOW()");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $message = 'Já existe um convite pendente para este e-mail. Por favor, verifique sua caixa de entrada ou aguarde 24 horas para solicitar um novo.';
            } else {
                // Insert new invitation
                $stmt = $pdo->prepare("INSERT INTO convite_convites (email, token, expira_em) VALUES (?, ?, ?)");
                $stmt->execute([$email, $token, $expiry]);
                
                // Send email with the link
                $to = $email;
                $subject = 'Seu link de acesso ao formulário de convidado';
                $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/convidado.php?token=" . $token;
                
                $message = "
                <html>
                <head>
                    <title>Seu link de acesso</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { 
                            display: inline-block; 
                            padding: 10px 20px; 
                            background-color: #2c3e50; 
                            color: white; 
                            text-decoration: none; 
                            border-radius: 5px; 
                            margin: 20px 0;
                        }
                        .footer { 
                            margin-top: 30px; 
                            font-size: 12px; 
                            color: #777; 
                            border-top: 1px solid #eee; 
                            padding-top: 10px;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Olá!</h2>
                        <p>Você solicitou acesso ao formulário de convidado. Clique no botão abaixo para acessar o formulário:</p>
                        <p><a href='$link' class='button'>Acessar Formulário</a></p>
                        <p>Ou copie e cole o seguinte link no seu navegador:</p>
                        <p><a href='$link'>$link</a></p>
                        <p>Este link é válido por 24 horas.</p>
                        <div class='footer'>
                            <p>Se você não solicitou este link, por favor, desconsidere este e-mail.</p>
                            <p>Atenciosamente,<br>Equipe Tlantic</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: Tlantic <noreply@tlantic.com>' . "\r\n";
                
                if (mail($to, $subject, $message, $headers)) {
                    $message = 'Um e-mail com o link de acesso foi enviado para ' . htmlspecialchars($email) . '. O link é válido por 24 horas.';
                } else {
                    $error = 'Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente mais tarde.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.';
            // Log the error
            error_log('Erro ao enviar convite: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Acesso - Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #fff;
            background-color: #1a237e;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            color: #333;
        }
        
        .guest-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .guest-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="guest-header">
            <h1><i class="bi bi-envelope-paper"></i> Solicitar Acesso</h1>
            <p class="text-muted">Receba um link para preencher o formulário de convidado</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email" class="form-label">Endereço de E-mail</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" required 
                       placeholder="seu@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <div class="form-text">Enviaremos um link de acesso para este e-mail.</div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-send"></i> Enviar Link de Acesso
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p>Já tem um link de acesso? <a href="convidado.php">Acesse o formulário aqui</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
