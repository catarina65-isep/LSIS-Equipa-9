 <?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Força o logout ao acessar a página de login
session_destroy();

// Inclui o arquivo de configuração
require_once __DIR__ . '/includes/permissions.php';

// Se o usuário já estiver logado, redireciona para o dashboard apropriado
if (isset($_SESSION['utilizador_id']) && isset($_SESSION['id_perfilacesso'])) {
    switch ($_SESSION['id_perfilacesso']) {
        case 1: // Admin
        case 3: // Coordenador
            header('Location: /LSIS-Equipa-9/UI/dashboard.php');
            break;
        case 2: // RH
            header('Location: /LSIS-Equipa-9/UI/rh.php');
            break;
        case 4: // Colaborador
            header('Location: /LSIS-Equipa-9/UI/colaborador.php');
            break;
        default:
            header('Location: /LSIS-Equipa-9/UI/index.php');
    }
    exit;
}

$page_title = "Login Plataforma - Tlantic";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .left-side {
            flex: 1;
            background: url('https://portugalstartups.com/wp-content/uploads/2015/09/instalacoes_tlantic_1366_137757024252e91bf77da19.jpg') no-repeat center center;
            background-size: cover;
        }

        .right-side {
            flex: 1;
            background: #e6f0ff; /* Azul claro */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: white;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .logo {
            max-width: 180px;
            margin-bottom: 25px;
        }

        .welcome-container {
            margin-bottom: 30px;
        }

        .welcome-emoji {
            font-size: 42px;
            margin-bottom: 15px;
            animation: wave 2s infinite;
        }

        .welcome-title {
            font-size: 28px;
            color: #2c3e50;
            margin: 0 0 8px 0;
            font-weight: 700;
        }

        .company-name {
            color: #0066ff;
            position: relative;
            display: inline-block;
        }

        .company-name:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            bottom: -4px;
            left: 0;
            background: linear-gradient(90deg, #0066ff, #00ccff);
            border-radius: 3px;
        }

        .welcome-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group img {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            opacity: 0.7;
        }

        input[type="text"], 
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 16px 50px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #0066ff;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            background-color: #fff;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: #0066ff;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            background: #0052cc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2);
        }

        .action-links {
            margin-top: 20px;
            text-align: center;
        }

        .action-link {
            display: block;
            color: #0066ff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            margin: 15px 0;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .action-link:hover {
            color: #004bb5;
            text-decoration: underline;
        }

        .guest-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #6c757d;
            font-size: 15px;
        }

        .guest-divider::before,
        .guest-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }

        .guest-divider span {
            padding: 0 10px;
        }

        /* Modal de Recuperação de Senha */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            position: relative;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .modal-subtitle {
            color: #7f8c8d;
            font-size: 15px;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
            background: none;
            border: none;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #2c3e50;
        }

        .success-message {
            display: none;
            text-align: center;
            padding: 20px 0;
        }

        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }

        .success-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .success-text {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(10deg); }
            75% { transform: rotate(-10deg); }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left-side {
                min-height: 200px;
            }
            
            .right-side {
                padding: 30px 20px;
                justify-content: flex-start;
            }
            
            .login-card {
                padding: 30px 25px;
                box-shadow: none;
            }

            .modal-content {
                margin: 20px;
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }
            
            .welcome-title {
                font-size: 26px;
            }
            
            .welcome-emoji {
                font-size: 36px;
            }

            .modal-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-side"></div>
        <div class="right-side">
            <div class="login-card">
                <img class="logo" src="https://ixtenso.com/media/story/18141/content-1401199148_hires.jpg" alt="Tlantic Logo" />
                
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['erro']); ?>
                        <?php unset($_SESSION['erro']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="welcome-container">
                    <div class="welcome-emoji">👋</div>
                    <h1 class="welcome-title">Bem-vindo(a) à <span class="company-name">Tlantic</span></h1>
                    <p class="welcome-subtitle">Sua plataforma de gestão de colaboradores</p>
                </div>
                
                <?php if (!empty($erro)): ?>
                    <div class="error-message" style="background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #ef9a9a;">
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>
                
                <form id="loginForm" action="/LSIS-Equipa-9/UI/processa_login.php" method="POST">
                    <div class="form-group">
                        <div class="input-group">
                            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Email" />
                            <input type="email" name="email" id="loginEmail" placeholder="Email" required />
                        </div>

                        <div class="input-group">
                            <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" alt="Senha" />
                            <input type="password" name="senha" id="loginPassword" placeholder="Palavra-passe" required />
                        </div>


                    </div>

                    <button type="submit" class="submit-btn">Entrar</button>
                </form>

                <div class="action-links">
                    <a href="#" id="forgotPassword" class="action-link">Esqueceu a palavra-passe?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recuperação de Senha -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" id="closeModal">&times;</button>
            
            <div class="modal-header">
                <div class="welcome-emoji">🔑</div>
                <h2 class="modal-title">Recuperar Palavra-passe</h2>
                <p class="modal-subtitle">Insira o seu email para receber as instruções de recuperação</p>
            </div>
            
            <form id="forgotPasswordForm" action="recuperar_senha.php" method="POST">
                <div class="input-group" style="margin-bottom: 25px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Email" />
                    <input type="email" name="email" id="recoveryEmail" placeholder="Seu email" required />
                </div>
                
                <button type="submit" class="submit-btn">Enviar Instruções</button>
            </form>
            
            <div class="success-message" id="successMessage">
                <div class="success-icon">✓</div>
                <h3 class="success-title">Email Enviado!</h3>
                <p class="success-text">Por favor, verifique a sua caixa de entrada e siga as instruções para redefinir a sua palavra-passe.</p>
                <button class="submit-btn" style="max-width: 200px; margin: 0 auto;" id="closeSuccess">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        // Elementos do DOM
        const loginForm = document.getElementById('loginForm');
        const forgotPasswordLink = document.getElementById('forgotPassword');
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        const closeModal = document.getElementById('closeModal');
        const closeSuccess = document.getElementById('closeSuccess');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const successMessage = document.getElementById('successMessage');
        const recoveryEmail = document.getElementById('recoveryEmail');

        // Abrir modal de recuperação de senha
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            forgotPasswordModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        // Fechar modal
        function closeForgotPasswordModal() {
            forgotPasswordModal.classList.remove('show');
            document.body.style.overflow = 'auto';
            // Resetar formulário ao fechar
            forgotPasswordForm.reset();
            successMessage.style.display = 'none';
            forgotPasswordForm.style.display = 'block';
        }

        closeModal.addEventListener('click', closeForgotPasswordModal);
        closeSuccess.addEventListener('click', closeForgotPasswordModal);

        // Fechar modal ao clicar fora
        window.addEventListener('click', function(e) {
            if (e.target === forgotPasswordModal) {
                closeForgotPasswordModal();
            }
        });

        // Submeter formulário de recuperação de senha
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = recoveryEmail.value.trim();
            
            if (!email) {
                alert('Por favor, insira o seu email.');
                return;
            }
            
            // Simular envio do email
            console.log('Enviando email de recuperação para:', email);
            
            // Mostrar mensagem de sucesso
            forgotPasswordForm.style.display = 'none';
            successMessage.style.display = 'block';
        });


    </script>
</body>
</html>