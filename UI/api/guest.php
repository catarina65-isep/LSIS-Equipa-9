<?php
session_start();
require_once '../../BLL/guestBLL.php';
require_once '../../BLL/visitBLL.php';
require_once '../../BLL/cvBLL.php';
require_once '../../DAL/Database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        // Processar upload do CV
        $cvFile = $_FILES['cv'] ?? null;
        $cvPath = null;
        
        if ($cvFile && $cvFile['error'] === UPLOAD_ERR_OK) {
            if ($cvFile['type'] !== 'application/pdf') {
                throw new Exception('Apenas arquivos PDF são aceitos.');
            }
            
            if ($cvFile['size'] > 5 * 1024 * 1024) { // 5MB
                throw new Exception('O arquivo é muito grande. O máximo permitido é 5MB.');
            }
            
            $uploadDir = '../../uploads/cvs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($cvFile['name']);
            $cvPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($cvFile['tmp_name'], $cvPath)) {
                throw new Exception('Erro ao fazer upload do arquivo.');
            }
        }

        // Processar dados do formulário
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'visit_date' => $_POST['visitDate'] ?? '',
            'visit_time' => $_POST['visitTime'] ?? '',
            'purpose' => $_POST['purpose'] ?? '',
            'cv_path' => $cvPath
        ];

        // Validar dados
        if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || 
            empty($data['visit_date']) || empty($data['visit_time']) || empty($data['purpose'])) {
            throw new Exception('Por favor, preencha todos os campos obrigatórios.');
        }

        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Por favor, insira um email válido.');
        }

        // Validar data e hora
        $visitDateTime = $data['visit_date'] . ' ' . $data['visit_time'];
        if (strtotime($visitDateTime) < time()) {
            throw new Exception('A data e hora da visita devem ser no futuro.');
        }

        // Salvar dados
        $db = new Database();
        $conn = $db->getConnection();

        // Salvar CV primeiro para obter o ID
        $stmt = $conn->prepare("INSERT INTO cv (path) VALUES (:path)");
        $stmt->execute([':path' => $data['cv_path']]);
        $cvId = $conn->lastInsertId();
        
        // Salvar dados do convidado
        $stmt = $conn->prepare("INSERT INTO convidado (nome, email, telefone) VALUES (:nome, :email, :telefone)");
        $stmt->execute([
            ':nome' => $data['name'],
            ':email' => $data['email'],
            ':telefone' => $data['phone']
        ]);
        $guestId = $conn->lastInsertId();
        
        // Salvar visita
        $stmt = $conn->prepare("INSERT INTO visita (id_convidado, data_visita, hora_visita, motivo_visita, id_cv) VALUES (:id_convidado, :data_visita, :hora_visita, :motivo_visita, :id_cv)");
        $stmt->execute([
            ':id_convidado' => $guestId,
            ':data_visita' => $data['visit_date'],
            ':hora_visita' => $data['visit_time'],
            ':motivo_visita' => $data['purpose'],
            ':id_cv' => $cvId
        ]);

        echo json_encode(['success' => true, 'message' => 'Visita agendada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
