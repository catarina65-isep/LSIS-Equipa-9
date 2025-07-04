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
    $function = isset($_GET['function']) ? $_GET['function'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    // Construir cláusula WHERE
    $where = [];
    $params = [];

    if ($startDate) {
        $where[] = "data_entrada >= :startDate";
        $params[':startDate'] = $startDate;
    }

    if ($endDate) {
        $where[] = "data_entrada <= :endDate";
        $params[':endDate'] = $endDate;
    }

    if ($department) {
        $where[] = "id_departamento = :department";
        $params[':department'] = $department;
    }

    if ($function) {
        $where[] = "id_funcao = :function";
        $params[':function'] = $function;
    }

    if ($status) {
        $where[] = "ativo = :status";
        $params[':status'] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Consultar relatórios de colaboradores
    $stmt = $conn->prepare("
        SELECT 
            c.id_colaborador as id,
            c.nome,
            c.email,
            c.telefone,
            d.nome as departamento,
            f.nome as funcao,
            DATE_FORMAT(c.data_entrada, '%d/%m/%Y') as data_entrada,
            DATE_FORMAT(c.data_saida, '%d/%m/%Y') as data_saida,
            c.ativo,
            c.data_criacao
        FROM colaborador c
        LEFT JOIN departamento d ON c.id_departamento = d.id_departamento
        LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
        $whereClause
        ORDER BY c.data_entrada DESC
    ");

    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular métricas para o dashboard
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_collaborators,
            SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as active_collaborators,
            SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as inactive_collaborators,
            AVG(TIMESTAMPDIFF(DAY, data_entrada, data_saida)) as average_tenure
        FROM colaborador
        $whereClause
    ");

    $stmt->execute($params);
    $dashboard = $stmt->fetch(PDO::FETCH_ASSOC);

    // Arredondar média para 1 decimal
    $dashboard['average_tenure'] = round($dashboard['average_tenure'], 1);

    echo json_encode([
        'reports' => $reports,
        'dashboard' => $dashboard
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
