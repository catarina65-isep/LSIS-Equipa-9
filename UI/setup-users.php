<?php
require_once '../DAL/Database.php';
require_once '../DAL/LoginDAL.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Iniciar transação
    $conn->beginTransaction();

    // Criar perfis se não existirem
    $perfis = [
        ['nome' => 'Recursos Humanos', 'descricao' => 'Perfil para gestão de recursos humanos', 'id_perfil' => 'recursoshumanos'],
        ['nome' => 'Administrador', 'descricao' => 'Perfil administrador do sistema', 'id_perfil' => 'administrador']
    ];

    foreach ($perfis as $perfil) {
        $stmt = $conn->prepare("
            INSERT INTO perfilacesso (nome, descricao, id_perfil)
            VALUES (:nome, :descricao, :id_perfil)
            ON DUPLICATE KEY UPDATE 
            nome = :nome,
            descricao = :descricao
        ");
        $stmt->execute($perfil);
    }

    // Criar usuários de teste
    $usuarios = [
        [
            'email' => 'rh@recursoshumanos.com',
            'senha' => 'senha123', // Senha em texto simples
            'nome' => 'Responsável RH',
            'id_perfil' => 'recursoshumanos'
        ],
        [
            'email' => 'admin@administrador.com',
            'senha' => 'admin123', // Senha em texto simples
            'nome' => 'Administrador',
            'id_perfil' => 'administrador'
        ]
    ];

    foreach ($usuarios as $usuario) {
        // Criptografar a senha usando MD5 (como está configurado no sistema)
        $senha_hash = md5($usuario['senha']);
        
        $stmt = $conn->prepare("
            INSERT INTO utilizador (email, password_hash, nome, id_perfil_acesso, ativo)
            VALUES (:email, :senha_hash, :nome, 
                (SELECT id_perfil_acesso FROM perfilacesso WHERE id_perfil = :id_perfil),
                1)
            ON DUPLICATE KEY UPDATE 
            password_hash = :senha_hash,
            nome = :nome,
            id_perfil_acesso = (SELECT id_perfil_acesso FROM perfilacesso WHERE id_perfil = :id_perfil)
        ");
        
        $stmt->execute([
            'email' => $usuario['email'],
            'senha_hash' => $senha_hash,
            'nome' => $usuario['nome'],
            'id_perfil' => $usuario['id_perfil']
        ]);
    }

    // Confirmar transação
    $conn->commit();
    
    echo "Usuários configurados com sucesso!\n";
    echo "Agora você pode acessar o sistema com:\n";
    echo "- Email: rh@recursoshumanos.com\n";
    echo "- Senha: senha123\n\n";
    echo "- Email: admin@administrador.com\n";
    echo "- Senha: admin123\n";

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollBack();
    echo "Erro ao configurar usuários: " . $e->getMessage();
}
?>
