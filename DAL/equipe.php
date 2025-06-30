<?php
require_once '../config/config.php';

// Função para obter dados da equipe do coordenador
function getEquipeDados($coordenadorId) {
    global $conn;
    
    // Primeiro obter as equipes que o coordenador gerencia
    $stmt = $conn->prepare("
        SELECT e.id as equipe_id, e.nome as equipe_nome
        FROM equipes e
        JOIN coordenadores_equipes ce ON e.id = ce.id_equipe
        WHERE ce.id_coordenador = ?
    ");
    $stmt->bind_param("i", $coordenadorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipes = $result->fetch_all(MYSQLI_ASSOC);
    
    // Obter colaboradores dessas equipes
    $equipeIds = array_column($equipes, 'equipe_id');
    if (empty($equipeIds)) {
        return ['colaboradores' => []];
    }
    
    $stmt = $conn->prepare("
        SELECT c.id, c.nome, c.funcao, c.data_entrada, c.data_nascimento,
               c.status, e.nome as equipe_nome
        FROM colaboradores c
        JOIN equipes e ON c.id_equipe = e.id
        WHERE e.id IN (" . implode(',', array_fill(0, count($equipeIds), '?')) . ")
    ");
    
    $stmt->bind_param(str_repeat('i', count($equipeIds)), ...$equipeIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $colaboradores = $result->fetch_all(MYSQLI_ASSOC);
    
    return [
        'equipes' => $equipes,
        'colaboradores' => $colaboradores
    ];
}

// Função para obter alertas pendentes
function getAlertasPendentes($coordenadorId) {
    global $conn;
    
    // Obter equipes do coordenador
    $stmt = $conn->prepare("
        SELECT e.id as equipe_id
        FROM equipes e
        JOIN coordenadores_equipes ce ON e.id = ce.id_equipe
        WHERE ce.id_coordenador = ?
    ");
    $stmt->bind_param("i", $coordenadorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipes = $result->fetch_all(MYSQLI_ASSOC);
    
    $equipeIds = array_column($equipes, 'equipe_id');
    if (empty($equipeIds)) return [];
    
    // Obter alertas pendentes
    $stmt = $conn->prepare("
        SELECT a.*, ac.data_leitura, ac.lido
        FROM alertas a
        JOIN alertas_colaborador ac ON a.id = ac.id_alerta
        JOIN colaboradores c ON ac.id_colaborador = c.id
        WHERE c.id_equipe IN (" . implode(',', array_fill(0, count($equipeIds), '?')) . ")
        AND ac.lido = FALSE
    ");
    
    $stmt->bind_param(str_repeat('i', count($equipeIds)), ...$equipeIds);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Função para marcar alerta como lido
function marcarAlertaComoLido($alertaId, $colaboradorId) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE alertas_colaborador
        SET lido = TRUE, data_leitura = NOW()
        WHERE id_alerta = ? AND id_colaborador = ?
    ");
    
    $stmt->bind_param("ii", $alertaId, $colaboradorId);
    return $stmt->execute();
}

// Função para gerar alerta automático
function gerarAlerta($titulo, $descricao, $tipo, $colaboradorId) {
    global $conn;
    
    // Inserir alerta
    $stmt = $conn->prepare("
        INSERT INTO alertas (titulo, descricao, tipo)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $titulo, $descricao, $tipo);
    $stmt->execute();
    $alertaId = $conn->insert_id;
    
    // Inserir relação com o colaborador
    $stmt = $conn->prepare("
        INSERT INTO alertas_colaborador (id_alerta, id_colaborador)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $alertaId, $colaboradorId);
    $stmt->execute();
    
    return $alertaId;
}
?>
