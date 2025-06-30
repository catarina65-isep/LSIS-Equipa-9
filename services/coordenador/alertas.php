<?php
require_once '../../config/config.php';
require_once '../../DAL/equipe.php';
require_once '../../DAL/alertas.php';

// Verifica autenticação
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// Processa diferentes ações
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'marcar-como-lido':
        $alertaId = $_POST['alertaId'] ?? 0;
        $colaboradorId = $_POST['colaboradorId'] ?? 0;
        
        if ($alertaId && $colaboradorId) {
            if (marcarAlertaComoLido($alertaId, $colaboradorId)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao marcar alerta como lido']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
        }
        break;

    case 'gerar-alerta':
        $tipo = $_POST['tipo'] ?? '';
        $colaboradorId = $_POST['colaboradorId'] ?? 0;
        
        if ($tipo && $colaboradorId) {
            $titulo = getTituloAlerta($tipo);
            $descricao = getDescricaoAlerta($tipo, $colaboradorId);
            
            $alertaId = gerarAlerta($titulo, $descricao, $tipo, $colaboradorId);
            
            if ($alertaId) {
                echo json_encode(['success' => true, 'alertaId' => $alertaId]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao gerar alerta']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Ação não suportada']);
}

// Funções auxiliares para gerar alertas
function getTituloAlerta($tipo) {
    $titulos = [
        'aniversario' => 'Aniversário do Colaborador',
        'contrato' => 'Atualização Contratual',
        'voucher' => 'Voucher de Telemóvel',
        'documento' => 'Atualização de Documento'
    ];
    return $titulos[$tipo] ?? 'Alerta do Sistema';
}

function getDescricaoAlerta($tipo, $colaboradorId) {
    global $conn;
    
    // Obter nome do colaborador
    $stmt = $conn->prepare("SELECT nome FROM colaboradores WHERE id = ?");
    $stmt->bind_param("i", $colaboradorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $colaborador = $result->fetch_assoc();
    
    $mensagens = [
        'aniversario' => "O colaborador {$colaborador['nome']} está prestes a fazer aniversário.",
        'contrato' => "O contrato do colaborador {$colaborador['nome']} precisa ser atualizado.",
        'voucher' => "O voucher de telemóvel do colaborador {$colaborador['nome']} está prestes a expirar.",
        'documento' => "O documento do colaborador {$colaborador['nome']} precisa ser atualizado."
    ];
    
    return $mensagens[$tipo] ?? "Alerta do sistema para o colaborador {$colaborador['nome']}";
}
?>
