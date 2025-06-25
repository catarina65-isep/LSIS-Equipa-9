<?php
// Inicia a sessão
session_start();

// Se o usuário não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: UI/login.php');
    exit;
}

// Se estiver logado, mostra uma página simples
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - Tlantic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        p {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0066ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0052cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>!</h1>
        <p>Você está logado no sistema Tlantic.</p>
        <a href="UI/logout.php" class="btn">Sair</a>
    </div>
</body>
</html>
