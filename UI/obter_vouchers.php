<?php
session_start();
require_once '../DAL/config.php';

// Check if user is logged in
if (!isset($_SESSION['utilizador_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado. Sessão: ' . json_encode($_SESSION)]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get colaborador ID for the current user
    $stmt = $db->prepare("SELECT id_colaborador FROM colaborador WHERE id_utilizador = ?");
    $stmt->execute([$_SESSION['utilizador_id']]);
    $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$colaborador) {
        throw new Exception('Colaborador não encontrado');
    }
    
    // Get vouchers for this colaborador
    $stmt = $db->prepare("
        SELECT 
            v.*,
            CASE 
                WHEN v.tipo = 'telemovel' THEN 'Telemóvel'
                WHEN v.tipo = 'tvnetvoz' THEN 'TV+Net+Voz'
                WHEN v.tipo = 'netmovel' THEN 'Net Móvel'
                WHEN v.tipo = 'cinema' THEN 'Cinema'
                ELSE 'Outro'
            END as tipo_formatado
        FROM vouchers v
        WHERE v.id_colaborador = ?
        ORDER BY v.data_pedido DESC
    ");
    
    $stmt->execute([$colaborador['id_colaborador']]);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($vouchers);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
