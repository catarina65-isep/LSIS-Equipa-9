<?php
require_once '../config/config.php';
require_once '../config/alertas_config.php';
require_once '../DAL/alertas.php';

// Função para verificar se já existe um alerta recente
function existeAlertaRecente($tipo, $colaboradorId, $dias = 7) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT id FROM alertas_colaborador
        WHERE id_colaborador = ?
        AND id_alerta IN (
            SELECT id FROM alertas
            WHERE tipo = ?
            AND data_criacao >= DATE_SUB(NOW(), INTERVAL ? DAY)
        )
    ");
    
    $stmt->bind_param("isi", $colaboradorId, $tipo, $dias);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// Função para verificar aniversários
function verificarAniversarios() {
    global $conn;
    $config = require '../config/alertas_config.php';
    $alertaConfig = $config['aniversario'];
    
    // Obter colaboradores com aniversário nos próximos dias
    $stmt = $conn->prepare("
        SELECT id, nome, data_nascimento 
        FROM colaboradores 
        WHERE DATE_ADD(data_nascimento, INTERVAL YEAR(CURDATE()) - YEAR(data_nascimento) YEAR) 
        BETWEEN DATE_SUB(CURDATE(), INTERVAL ? DAY) 
        AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
    ");
    
    $diasAntes = $alertaConfig['antes'];
    $stmt->bind_param("ii", $diasAntes, $diasAntes);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($colaborador = $result->fetch_assoc()) {
        if (!existeAlertaRecente($alertaConfig['tipo'], $colaborador['id'])) {
            $titulo = $alertaConfig['assunto'];
            $descricao = "O colaborador {$colaborador['nome']} está prestes a fazer aniversário.";
            gerarAlerta($titulo, $descricao, $alertaConfig['tipo'], $colaborador['id']);
        }
    }
}

// Função para verificar vencimento de vouchers
function verificarVouchers() {
    global $conn;
    $config = require '../config/alertas_config.php';
    $alertaConfig = $config['voucher'];
    
    // Obter vouchers que vencem nos próximos meses
    $stmt = $conn->prepare("
        SELECT b.id_colaborador, c.nome 
        FROM beneficios b
        JOIN colaboradores c ON b.id_colaborador = c.id
        WHERE b.tipo = 'voucher'
        AND b.data_fim IS NOT NULL
        AND b.data_fim BETWEEN DATE_SUB(CURDATE(), INTERVAL ? MONTH)
        AND DATE_ADD(CURDATE(), INTERVAL ? MONTH)
    ");
    
    $mesesAntes = $alertaConfig['antes'];
    $stmt->bind_param("ii", $mesesAntes, $mesesAntes);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($colaborador = $result->fetch_assoc()) {
        if (!existeAlertaRecente($alertaConfig['tipo'], $colaborador['id_colaborador'])) {
            $titulo = $alertaConfig['assunto'];
            $descricao = "O voucher de telemóvel do colaborador {$colaborador['nome']} está prestes a expirar.";
            gerarAlerta($titulo, $descricao, $alertaConfig['tipo'], $colaborador['id_colaborador']);
        }
    }
}

// Função para verificar documentos
function verificarDocumentos() {
    global $conn;
    $config = require '../config/alertas_config.php';
    $alertaConfig = $config['documento'];
    
    // Obter documentos que vencem nos próximos dias
    $stmt = $conn->prepare("
        SELECT d.id_colaborador, c.nome 
        FROM documentos d
        JOIN colaboradores c ON d.id_colaborador = c.id
        WHERE d.data_validade IS NOT NULL
        AND d.data_validade BETWEEN DATE_SUB(CURDATE(), INTERVAL ? DAY)
        AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
    ");
    
    $diasAntes = $alertaConfig['antes'];
    $stmt->bind_param("ii", $diasAntes, $diasAntes);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($colaborador = $result->fetch_assoc()) {
        if (!existeAlertaRecente($alertaConfig['tipo'], $colaborador['id_colaborador'])) {
            $titulo = $alertaConfig['assunto'];
            $descricao = "O documento do colaborador {$colaborador['nome']} precisa ser atualizado.";
            gerarAlerta($titulo, $descricao, $alertaConfig['tipo'], $colaborador['id_colaborador']);
        }
    }
}

// Executar verificações
verificarAniversarios();
verificarVouchers();
verificarDocumentos();

// Registrar execução
$stmt = $conn->prepare("
    INSERT INTO historico_integracao (
        id_colaborador,
        tipo_operacao,
        dados,
        status
    ) VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", 0, 'alertas_automatizados', 'Verificação de alertas automatizados', 'sucesso');
$stmt->execute();
?>
