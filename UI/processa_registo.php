<?php
require_once 'config/database.php';

// Inicializar variáveis
$erro = '';
$sucesso = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário
    $nome = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo_usuario = $_POST['userType'] ?? '';
    $senha = $_POST['password'] ?? '';
    $confirmar_senha = $_POST['confirmPassword'] ?? '';
    
    // Validar dados
    if (empty($nome) || empty($email) || empty($tipo_usuario) || empty($senha) || empty($confirmar_senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, insira um e-mail válido.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Verificar se o e-mail já está em uso
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $erro = 'Este e-mail já está em uso. Por favor, utilize outro.';
            } else {
                // Criptografar senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir novo usuário
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nome, email, senha, tipo_usuario) 
                    VALUES (?, ?, ?, ?)
                ");
                
                if ($stmt->execute([$nome, $email, $senha_hash, $tipo_usuario])) {
                    $sucesso = 'Registro realizado com sucesso!';
                    
                    // Limpar formulário
                    $_POST = [];
                    
                    // Redirecionar após 2 segundos
                    header("refresh:2;url=login.php");
                } else {
                    $erro = 'Erro ao cadastrar usuário. Por favor, tente novamente.';
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro no banco de dados: ' . $e->getMessage();
        }
    }
}
?>