<?php
header('Content-Type: application/json');
require_once '../../DAL/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->query("
        SELECT id_funcao as id, nome 
        FROM funcao 
        WHERE ativo = 1 
        ORDER BY nome
    ");
    
    $functions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($functions);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
