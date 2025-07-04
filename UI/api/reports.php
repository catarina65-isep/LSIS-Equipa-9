<?php
header('Content-Type: application/json');
require_once '../../DAL/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Obter parâmetros de filtro
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
    $department = isset($_GET['department']) ? $_GET['department'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    // Construir cláusula WHERE
    $where = [];
    $params = [];

    if ($startDate) {
        $where[] = "data >= :startDate";
        $params[':startDate'] = $startDate;
    }

    if ($endDate) {
        $where[] = "data <= :endDate";
        $params[':endDate'] = $endDate;
    }

    if ($department) {
        $where[] = "id_departamento = :department";
        $params[':department'] = $department;
    }

    if ($status) {
        $where[] = "status = :status";
        $params[':status'] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Consultar relatórios
    $stmt = $conn->prepare("
        SELECT 
            r.id,
            DATE_FORMAT(r.data, '%d/%m/%Y') as data,
            d.nome as departamento,
            r.tipo_pedido,
            r.status,
            CASE 
                WHEN r.status = 'concluido' THEN 
                    TIMESTAMPDIFF(DAY, r.data, r.data_conclusao)
                ELSE NULL
            END as tempo_resolucao
        FROM pedidos r
        LEFT JOIN departamento d ON r.id_departamento = d.id_departamento
        $whereClause
        ORDER BY r.data DESC
    ");

    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular métricas para o dashboard
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pending_requests,
            SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as completed_requests,
            AVG(TIMESTAMPDIFF(DAY, data, data_conclusao)) as average_days
        FROM pedidos
        $whereClause
    ");

    $stmt->execute($params);
    $dashboard = $stmt->fetch(PDO::FETCH_ASSOC);

    // Arredondar tempo médio para 1 decimal
    $dashboard['average_days'] = round($dashboard['average_days'], 1);

    echo json_encode([
        'reports' => $reports,
        'dashboard' => $dashboard
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
