<?php
session_start();
require_once __DIR__ . '/../DAL/database.php';

// Redirecionar se já estiver logado
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
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f5f7fa, #e2ecf9);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            max-width: 900px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            overflow: hidden;
            width: 100%;
        }

        .left-side {
            background: url('https://ixtenso.com/media/story/18141/content-1401199148_hires.jpg') center/cover no-repeat;
            width: 45%;
        }

        .right-side {
            padding: 3rem;
            width: 55%;
        }

        .login-card {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            width: 120px;
            margin-bottom: 1rem;
        }

        .welcome-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1a202c;
        }

        .welcome-subtitle {
            font-size: 1rem;
            color: #718096;
            margin-bottom: 2rem;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e0;
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .input-group img {
            width: 20px;
            margin-right: 0.75rem;
        }

        .input-group input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 1rem;
        }

        .input-group input:focus {
            outline: none;
        }

        .submit-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #1557b0;
        }

        .action-links {
            margin-top: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
        }

        .action-link {
            color: #1a73e8;
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .alert {
            background-color: #fde8e8;
            color: #c53030;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            width: 100%;
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 90%;
            }

            .left-side {
                display: none;
            }

            .right-side {
                width: 100%;
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
                    <div class="alert">
                        <?= htmlspecialchars($_SESSION['erro']); ?>
                        <?php unset($_SESSION['erro']); ?>
                    </div>
                <?php endif; ?>

                <h1 class="welcome-title">Bem-vindo(a) à Tlantic</h1>
                <p class="welcome-subtitle">A sua plataforma de gestão de colaboradores</p>

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
                    </div>

                    <button type="submit" class="submit-btn">Entrar</button>
                </form>

                <div class="action-links">
                    <a href="#" id="forgotPassword" class="action-link">Esqueceu a palavra-passe?</a> |
                    <a href="registro.php" class="action-link">Criar uma conta</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
