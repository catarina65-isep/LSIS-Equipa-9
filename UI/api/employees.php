<?php
header('Content-Type: application/json');
require_once '../../DAL/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Listar colaboradores
            $search = $_GET['search'] ?? '';
            $department = $_GET['department'] ?? '';

            $query = "
                SELECT 
                    c.id_colaborador as id,
                    c.nome,
                    d.nome as departamento,
                    f.nome as funcao,
                    DATE_FORMAT(c.data_admissao, '%d/%m/%Y') as data_admissao
                FROM colaborador c
                LEFT JOIN departamento d ON c.id_departamento = d.id_departamento
                LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
                WHERE c.ativo = 1
            ";

            $params = [];
            $conditions = [];

            if (!empty($search)) {
                $conditions[] = "c.nome LIKE :search";
                $params[':search'] = "%{$search}%";
            }

            if (!empty($department)) {
                $conditions[] = "c.id_departamento = :department";
                $params[':department'] = $department;
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY c.nome";

            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($employees);
            break;

        case 'POST':
            // Adicionar novo colaborador
            $data = json_decode(file_get_contents('php://input'), true);

            $stmt = $conn->prepare("
                INSERT INTO colaborador (
                    nome,
                    email,
                    id_departamento,
                    id_funcao,
                    data_admissao,
                    data_nascimento,
                    ativo
                ) VALUES (
                    :nome,
                    :email,
                    :department,
                    :function,
                    :admissionDate,
                    :birthDate,
                    1
                )
            ");

            $stmt->execute([
                ':nome' => $data['name'],
                ':email' => $data['email'],
                ':department' => $data['department'],
                ':function' => $data['function'],
                ':admissionDate' => $data['admissionDate'],
                ':birthDate' => $data['birthDate']
            ]);

            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
