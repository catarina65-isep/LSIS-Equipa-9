<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set JSON header
header('Content-Type: application/json');

// Simple debug function
function log_debug($message) {
    $log = "[" . date('Y-m-d H:i:s') . "] $message\n";
    file_put_contents('voucher_debug.log', $log, FILE_APPEND);
    return $log;
}

// Log the request
log_debug("=== New Request ===");
log_debug("POST data: " . print_r($_POST, true));
log_debug("Raw input: " . file_get_contents('php://input'));
log_debug("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['utilizador_id'])) {
    $error = 'Usuário não está autenticado. Sessão: ' . json_encode($_SESSION);
    log_debug("Authentication error: $error");
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$id_utilizador = $_SESSION['utilizador_id'];
log_debug("User ID from session: $id_utilizador");

// Get form data
$data = json_decode(file_get_contents('php://input'), true);

// Simple validation
if (empty($data['tipo'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor, selecione o tipo de voucher']);
    exit;
}

if (empty($data['contacto'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha o contacto']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get colaborador ID for the current user
    $stmt = $db->prepare("SELECT id_colaborador FROM colaborador WHERE id_utilizador = ?");
    $stmt->execute([$id_utilizador]);
    $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$colaborador) {
        throw new Exception('Colaborador não encontrado');
    }
    
    // Insert the voucher request
    $stmt = $db->prepare("
        INSERT INTO vouchers 
        (id_colaborador, tipo, numero_cliente, contacto, observacoes, estado, data_pedido)
        VALUES (?, ?, ?, ?, ?, 'pendente', NOW())
    ");
    
    $success = $stmt->execute([
        $colaborador['id_colaborador'],
        $data['tipo'],
        $data['numero_cliente'] ?? null,
        $data['contacto'],
        $data['observacoes'] ?? null
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido de voucher enviado com sucesso!',
            'voucher_id' => $db->lastInsertId()
        ]);
    } else {
        throw new Exception('Erro ao processar o pedido');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
