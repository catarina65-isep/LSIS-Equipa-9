<?php
require_once '../config/config.php';

// Verifica se o perfil do coordenador existe
$stmt = $conn->prepare("SELECT id, nome FROM perfilacesso WHERE id = 3");
$stmt->execute();
$perfil = $stmt->fetch();

if (!$perfil) {
    // Se não existir, cria o perfil
    $stmt = $conn->prepare("
        INSERT INTO perfilacesso (nome, descricao)
        VALUES (?, ?)
    ");
    
    $descricao = "Coordenador de equipe com acesso restrito aos dados da sua equipe";
    $stmt->execute(["Coordenador", $descricao]);
    
    // Cria as permissões básicas para o coordenador
    $permissoes = [
        ['colaboradores', 'visualizar', true],
        ['documentos', 'visualizar', true],
        ['beneficios', 'visualizar', true],
        ['alertas', 'visualizar', true],
        ['equipe', 'gerenciar', true]
    ];
    
    $perfilId = $conn->lastInsertId();
    
    foreach ($permissoes as $permissao) {
        $stmt = $conn->prepare("
            INSERT INTO permissoes (id_perfilacesso, modulo, acao, permitido)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$perfilId, $permissao[0], $permissao[1], $permissao[2]]);
    }
    
    echo "Perfil de Coordenador criado com sucesso!\n";
} else {
    echo "Perfil de Coordenador já existe.\n";
}

// Verifica se o usuário coordenador@tlantic.pt existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute(["coordenador@tlantic.pt"]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // Se não existir, cria o usuário
    $senha = password_hash("senha123", PASSWORD_DEFAULT); // Senha padrão: senha123
    $stmt = $conn->prepare("
        INSERT INTO usuarios (nome, email, senha, id_perfilacesso)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute(["Coordenador Principal", "coordenador@tlantic.pt", $senha, 3]);
    echo "Usuário coordenador@tlantic.pt criado com sucesso!\n";
} else {
    echo "Usuário coordenador@tlantic.pt já existe.\n";
}
?>
