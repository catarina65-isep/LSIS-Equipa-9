<?php
header('Content-Type: application/json');
require_once '../../DAL/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->query("
        SELECT id_departamento as id, nome 
        FROM departamento 
        WHERE ativo = 1 
        ORDER BY nome
    ");
    
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($departments);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
