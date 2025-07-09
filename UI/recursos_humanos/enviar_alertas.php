<?php
// Iniciar sessão e verificar autenticação
session_start();
require_once __DIR__ . '/../../DAL/database.php';

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Enviar Alerta - Tlantic";
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mensagem = isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem'], ENT_QUOTES, 'UTF-8') : '';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um endereço de e-mail válido.';
    } else {
        try {
            $pdo = Database::getInstance();
            
            // Inclui o arquivo de configuração do PHPMailer
            require_once __DIR__ . '/../../DAL/PHPMailerConfig.php';
            
            if (!function_exists('sendEmail')) {
                error_log('ERRO: A função sendEmail não foi encontrada');
                $error = 'Erro de configuração do sistema. Por favor, tente novamente mais tarde.';
            } else {
                // Preparar o conteúdo do e-mail
                $assunto = "Alerta Importante - Recursos Humanos";
                $email_body = "
                <html>
                <head>
                    <title>Alerta Importante</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
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
                        <h2>Alerta Importante</h2>
                        <p>" . nl2br(htmlspecialchars($mensagem)) . "</p>
                        <div class='footer'>
                            <p>Este é um e-mail automático, por favor não responda.</p>
                            <p>Atenciosamente,<br>Equipa de Recursos Humanos<br>Tlantic</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                // Tentar enviar o e-mail
                $enviado = @sendEmail($email, $assunto, $email_body);
                
                if ($enviado) {
                    $message = 'Alerta enviado com sucesso para ' . htmlspecialchars($email);
                    
                    try {
                        // Verificar se a tabela existe antes de tentar inserir
                        $tableExists = $pdo->query("SHOW TABLES LIKE 'log_alertas'")->rowCount() > 0;
                        
                        if ($tableExists) {
                            $stmt = $pdo->prepare("INSERT INTO log_alertas (email, assunto, mensagem, enviado_por) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$email, $assunto, $mensagem, $_SESSION['utilizador_id']]);
                        }
                    } catch (PDOException $e) {
                        // Se houver erro ao gravar no log, apenas registra no log de erros, mas não mostra para o usuário
                        error_log('Erro ao registrar log de alerta: ' . $e->getMessage());
                    }
                } else {
                    $error = 'Ocorreu um erro ao enviar o alerta. Por favor, tente novamente.';
                    error_log('Falha ao enviar e-mail para: ' . $email);
                }
            }
            
        } catch (PDOException $e) {
            $error = 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.';
            error_log('Erro ao enviar alerta: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Alertas - Tlantic</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/LSIS-Equipa-9/UI/assets/css/style.css" rel="stylesheet">
    
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Enviar Alerta</h1>
            <div class="user-actions">
                <a href="/LSIS-Equipa-9/UI/logout.php" class="btn btn-outline-secondary btn-sm">
                    <i class='bx bx-log-out'></i> Sair
                </a>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Enviar Alerta por E-mail</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success">
                                    <i class='bx bx-check-circle me-2'></i>
                                    <?= htmlspecialchars($message) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class='bx bx-error-circle me-2'></i>
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail do Destinatário</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">
                                        Por favor, insira um endereço de e-mail válido.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="mensagem" class="form-label">Mensagem do Alerta</label>
                                    <textarea class="form-control" id="mensagem" name="mensagem" rows="6" required></textarea>
                                    <div class="invalid-feedback">
                                        Por favor, digite a mensagem do alerta.
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-send me-2'></i> Enviar Alerta
                                    </button>
                                    <a href="alertas.php" class="btn btn-outline-secondary">
                                        <i class='bx bx-arrow-back me-2'></i> Voltar
                                    </a>
                                </div>
                            </form>
                            
                            <script>
                            // Validação do formulário
                            (function () {
                                'use strict'
                                
                                var forms = document.querySelectorAll('.needs-validation')
                                
                                Array.prototype.slice.call(forms)
                                    .forEach(function (form) {
                                        form.addEventListener('submit', function (event) {
                                            if (!form.checkValidity()) {
                                                event.preventDefault()
                                                event.stopPropagation()
                                            }
                                            
                                            form.classList.add('was-validated')
                                        }, false)
                                    })
                            })()
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }
        });
    </script>
</body>
</html>
