<?php
header('Content-Type: application/json');
require_once '../../DAL/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Total de colaboradores
    $stmt = $conn->query("SELECT COUNT(*) as total FROM colaborador");
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Aniversários do mês
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM colaborador 
        WHERE MONTH(data_nascimento) = MONTH(CURRENT_DATE())
    ");
    $birthdays = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Alertas pendentes
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM alerta 
        WHERE data_alerta >= CURRENT_DATE() 
        AND lido = 0
    ");
    $alerts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'totalEmployees' => $totalEmployees,
        'birthdaysThisMonth' => $birthdays,
        'pendingAlerts' => $alerts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
