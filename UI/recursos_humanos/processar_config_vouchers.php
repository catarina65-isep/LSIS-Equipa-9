<?php
require_once __DIR__ . '/../../DAL/Database.php';

// Verificar se o usuário está autenticado e tem permissão
session_start();
if (!isset($_SESSION['id_utilizador']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    $_SESSION['erro'] = 'Acesso não autorizado.';
    header('Location: index.php');
    exit();
}

// Verificar se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = 'Método não permitido.';
    header('Location: relatorios.php#vouchers');
    exit();
}

// Validar e obter o valor do formulário
$totalVouchers = filter_input(INPUT_POST, 'totalVouchers', FILTER_VALIDATE_INT);

if ($totalVouchers === false || $totalVouchers < 0) {
    $_SESSION['erro'] = 'Valor inválido para o total de vouchers. Deve ser um número inteiro positivo.';
    header('Location: relatorios.php?erro=' . urlencode($_SESSION['erro']) . '#vouchers');
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se já existe um registro de configuração
    $stmt = $db->prepare("SELECT id FROM configuracao WHERE chave = 'total_vouchers_disponiveis'");
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($config) {
        // Atualizar o valor existente
        $stmt = $db->prepare("UPDATE configuracao SET valor = :valor, atualizado_em = NOW() WHERE chave = 'total_vouchers_disponiveis'");
    } else {
        // Inserir um novo registro
        $stmt = $db->prepare("INSERT INTO configuracao (chave, valor, criado_em, atualizado_em) 
                             VALUES ('total_vouchers_disponiveis', :valor, NOW(), NOW())");
    }
    
    $stmt->bindParam(':valor', $totalVouchers, PDO::PARAM_INT);
    $stmt->execute();
    
    // Redirecionar com mensagem de sucesso
    $_SESSION['sucesso'] = 'Configuração de vouchers atualizada com sucesso!';
    header('Location: relatorios.php?sucesso=true#vouchers');
    exit();
    
} catch (PDOException $e) {
    error_log('Erro ao salvar configuração de vouchers: ' . $e->getMessage());
    
    // Redirecionar com mensagem de erro
    $_SESSION['erro'] = 'Erro ao salvar a configuração. Por favor, tente novamente.';
    header('Location: relatorios.php?erro=' . urlencode($_SESSION['erro']) . '#vouchers');
    exit();
}
