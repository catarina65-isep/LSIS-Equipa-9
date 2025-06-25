<?php
session_start();
require_once __DIR__ . '/../DAL/database.php';

// Verificar se já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
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
        <?php include __DIR__ . '/assets/css/style.css'; ?>
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
                
                <form id="loginForm" action="processa_login.php" method="POST">
                    <div class="form-group">
                        <div class="input-group">
                            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Email" />
                            <input type="email" name="email" id="loginEmail" placeholder="Email" required />
                        </div>

                        <div class="input-group">
                            <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" alt="Senha" />
                            <input type="password" name="senha" id="loginPassword" placeholder="Palavra-passe" required />
                        </div>

                        <div class="input-group">
                            <select name="tipo_usuario" id="loginUserType" required>
                                <option value="">Selecione o seu perfil</option>
                                <option value="1">Colaborador</option>
                                <option value="2">Coordenador</option>
                                <option value="3">Recursos Humanos</option>
                                <option value="4">Administrador</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Entrar</button>
                </form>

                <div class="action-links">
                    <a href="#" id="forgotPassword" class="action-link">Esqueceu a palavra-passe?</a>
                    
                    <div class="guest-divider">
                        <span>ou</span>
                    </div>
                    
                    <a href="#" id="guestLink" class="action-link">Acesso Convidado</a>
                    <a href="registro.php" class="action-link">Criar uma conta</a>
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
        <?php include __DIR__ . '/assets/js/script.js'; ?>
    </script>
</body>
</html>