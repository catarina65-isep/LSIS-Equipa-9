<?php
session_start();
require_once __DIR__ . '/DAL/config.php';

$pdo = Database::getInstance();

echo "<h1>Verificação de Equipas</h1>";

// Verificar usuário logado
if (isset($_SESSION['utilizador_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE id_utilizador = ?");
    $stmt->execute([$_SESSION['utilizador_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Usuário Logado:</h2>";
    echo "<pre>";
    print_r($usuario);
    echo "</pre>";
    
    // Verificar se é coordenador
    $stmt = $pdo->prepare("SELECT * FROM coordenador WHERE id_utilizador = ?");
    $stmt->execute([$_SESSION['utilizador_id']]);
    $coordenador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($coordenador) {
        echo "<h3>Dados do Coordenador:</h3>";
        echo "<pre>";
        print_r($coordenador);
        echo "</pre>";
        
        // Mostrar equipe associada
        if (!empty($coordenador['id_equipa'])) {
            $stmt = $pdo->prepare("SELECT * FROM equipa WHERE id_equipa = ?");
            $stmt->execute([$coordenador['id_equipa']]);
            $equipa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h3>Equipa Associada:</h3>";
            echo "<pre>";
            print_r($equipa);
            echo "</pre>";
        } else {
            echo "<p>Este coordenador não está associado a nenhuma equipa.</p>";
        }
    } else {
        echo "<p>Este usuário não é um coordenador.</p>";
    }
} else {
    echo "<p>Nenhum usuário logado.</p>";
}

echo "<hr>";;

try {
    // Mostrar todas as equipes e seus coordenadores
    echo "<h2>Todas as Equipes e seus Coordenadores:</h2>";
    $stmt = $pdo->query("SELECT e.*, 
                                 c.id_coordenador, 
                                 c.id_utilizador, 
                                 u.username as coordenador_username,
                                 u.email as coordenador_email
                          FROM equipa e
                          LEFT JOIN coordenador c ON e.id_coordenador = c.id_coordenador
                          LEFT JOIN utilizador u ON c.id_utilizador = u.id_utilizador
                          WHERE e.ativo = 1");
    $equipas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>ID Equipa</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>ID Coordenador</th>
                <th>Usuário Coordenador</th>
                <th>Email Coordenador</th>
            </tr>";
    
    foreach ($equipas as $equipa) {
        echo "<tr>
                <td>{$equipa['id_equipa']}</td>
                <td>{$equipa['nome']}</td>
                <td>{$equipa['descricao']}</td>
                <td>{$equipa['id_coordenador']}</td>
                <td>{$equipa['coordenador_username']}</td>
                <td>{$equipa['coordenador_email']}</td>
              </tr>";
    }
    echo "</table>";
    
    // Mostrar todos os coordenadores
    echo "<h2>Todos os Coordenadores:</h2>";
    $stmt = $pdo->query("SELECT c.*, u.username, u.email 
                         FROM coordenador c 
                         JOIN utilizador u ON c.id_utilizador = u.id_utilizador 
                         WHERE c.ativo = 1");
    $coordenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>ID Coordenador</th>
                <th>ID Usuário</th>
                <th>Username</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>ID Equipa</th>
            </tr>";
    
    foreach ($coordenadores as $coordenador) {
        echo "<tr>
                <td>{$coordenador['id_coordenador']}</td>
                <td>{$coordenador['id_utilizador']}</td>
                <td>{$coordenador['username']}</td>
                <td>{$coordenador['email']}</td>
                <td>{$coordenador['cargo']}</td>
                <td>{$coordenador['id_equipa']}</td>
              </tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2>Erro:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
