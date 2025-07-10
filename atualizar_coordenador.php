<?php
require_once __DIR__ . '/DAL/config.php';

$pdo = Database::getInstance();

try {
    // Atualizar a coordenadora Ana Martins (ID 1) para apontar para a equipa1 (ID 117)
    $sql = "UPDATE coordenador SET id_equipa = 117 WHERE id_coordenador = 1";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();
    
    if ($result) {
        echo "Coordenadora atualizada com sucesso!<br>";
        echo "ID da Coordenadora: 1 (ana.martins@tlantic.pt)<br>";
        echo "ID da Equipa Associada: 117 (equipa1 - vendas)<br><br>";
        
        // Mostrar os dados atualizados
        $stmt = $pdo->query("SELECT c.*, u.email FROM coordenador c JOIN utilizador u ON c.id_utilizador = u.id_utilizador WHERE c.id_coordenador = 1");
        $coordenador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Dados Atualizados da Coordenadora:<br>";
        echo "<pre>";
        print_r($coordenador);
        echo "</pre>";
        
        // Mostrar dados da equipe
        $stmt = $pdo->query("SELECT * FROM equipa WHERE id_equipa = 117");
        $equipa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Dados da Equipa:<br>";
        echo "<pre>";
        print_r($equipa);
        echo "</pre>";
        
        echo "<p><a href='UI/coordenador/' target='_blank'>Ir para o Painel do Coordenador</a></p>";
    } else {
        echo "Erro ao atualizar a coordenadora.";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
