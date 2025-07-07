<?php
session_start();
require_once __DIR__ . '/../../DAL/database.php';

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Enviar Convite - Tlantic";
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
                
                // Construir a URL base corretamente
                $protocol = 'http';
                $host = 'localhost:8888'; // Usando localhost:8888 para garantir que funcione no MAMP
                $path = '/LSIS-Equipa-9/UI/convidado.php';
                $link = "$protocol://$host$path?token=$token";
                
                // O link foi gerado e será enviado por e-mail
                
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
                
                // Inclui o arquivo de configuração do PHPMailer
                require_once __DIR__ . '/../../DAL/PHPMailerConfig.php';
                
                // Corpo do e-mail em HTML
                $email_body = "
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
                        <p>Recebeu um convite para aceder ao formulário. Por favor preencha com todos os seus dados.</p>
                        <p>Clique no botão abaixo para acessar o formulário:</p>
                        <p><a href='$link' class='button'>Aceder ao Formulário</a></p>
                        <p>Ou copie e cole o link abaixo no seu navegador:</p>
                        <p><a href='$link'>$link</a></p>
                        <div class='footer'>
                            <p>Se você não solicitou este link, por favor, ignore este e-mail.</p>
                            <p>Atenciosamente,<br>Equipa Tlantic</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                // Debug: Verificar se a função sendEmail existe
                if (!function_exists('sendEmail')) {
                    error_log('ERRO: A função sendEmail não foi encontrada');
                    $error = 'Erro de configuração do sistema. Por favor, tente novamente mais tarde.';
                } else {
                    // Corrigindo a variável $to que estava indefinida
                    $to = $email;
                    error_log('Tentando enviar e-mail para: ' . $to);
                    error_log('Assunto: ' . $subject);
                    error_log('Corpo do e-mail: ' . substr($email_body, 0, 200) . '...');
                    
                    // Envia o e-mail usando PHPMailer
                    try {
                        $enviado = sendEmail($to, $subject, $email_body);
                        error_log('Resultado do envio: ' . ($enviado ? 'Sucesso' : 'Falha'));
                        
                        if ($enviado) {
                            $message = 'Um e-mail com o link de acesso foi enviado para ' . htmlspecialchars($email) . '. O link é válido por 24 horas.';
                        } else {
                            $error = 'Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente mais tarde. Verifique também a pasta de spam.';
                            error_log('Falha ao enviar e-mail. Verifique os logs do PHPMailer.');
                        }
                    } catch (Exception $e) {
                        $error = 'Erro ao tentar enviar o e-mail: ' . $e->getMessage();
                        error_log('Exceção ao enviar e-mail: ' . $e->getMessage());
                    }
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
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #ef476f;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
        }
        
        .form-control, .form-select {
            padding: 0.6rem 1rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Enviar Convite</h1>
                        <p class="text-muted mb-0">Insira o email do convidado para este receber o formulário</p>
                    </div>
                </div>
                
                <div class="container-fluid px-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class='bx bx-check-circle me-2'></i> <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class='bx bx-error-circle me-2'></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Informações do Convite</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Endereço de E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           placeholder="exemplo@email.com" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="form-text mt-2">Um link de acesso será enviado para o e-mail informado.</div>
                                </div>
            
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-send me-2'></i> Enviar Convite
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Inicializa os tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
