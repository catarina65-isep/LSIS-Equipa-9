<?php
session_start();
require_once '../../config/config.php';

// Verifica se o usuário está logado e tem perfil de coordenador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// Processa diferentes ações
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'dashboard':
        // Retorna dados completos para o dashboard
        $coordenadorId = $_SESSION['usuario_id'];
        $dados = getDashboardData($coordenadorId);
        echo json_encode($dados);
        break;

    case 'marcar-alerta-como-lido':
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

    case 'ver-alerta':
        $alertaId = $_POST['alertaId'] ?? 0;
        
        if ($alertaId) {
            $alerta = getAlertaDetalhes($alertaId);
            echo json_encode($alerta);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID do alerta inválido']);
        }
        break;

    case 'ver-documento':
        $documentoId = $_POST['documentoId'] ?? 0;
        
        if ($documentoId) {
            $documento = getDocumentoDetalhes($documentoId);
            echo json_encode($documento);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID do documento inválido']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Ação não suportada']);
}

// Função para obter dados completos do dashboard
function getDashboardData($coordenadorId) {
    global $conn;
    
    $dados = [];
    
    // Obter equipes do coordenador
    $stmt = $conn->prepare("
        SELECT c.id_equipa
        FROM coordenador c
        WHERE c.id_utilizador = ?
    ");
    $stmt->bind_param("i", $coordenadorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipes = $result->fetch_all(MYSQLI_ASSOC);
    
    $equipeIds = array_column($equipes, 'id_equipa');
    if (empty($equipeIds)) return ['error' => 'Nenhuma equipe encontrada'];
    
    // Obter colaboradores das equipes
    $stmt = $conn->prepare("
        SELECT c.*, e.nome as equipe_nome, f.titulo as funcao_nome
        FROM colaborador c
        JOIN equipa e ON c.id_equipa = e.id_equipa
        JOIN funcao f ON c.id_funcao = f.id_funcao
        WHERE c.id_equipa IN (" . implode(',', array_fill(0, count($equipeIds), '?')) . ")
    ");
    $stmt->bind_param(str_repeat('i', count($equipeIds)), ...$equipeIds);
    $stmt->execute();
    $colaboradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calcular estatísticas
    $dados['totalColaboradores'] = count($colaboradores);
    $dados['mediaIdade'] = calculateMediaIdade($colaboradores);
    $dados['mediaTempo'] = calculateMediaTempo($colaboradores);
    
    // Obter alertas pendentes
    $stmt = $conn->prepare("
        SELECT a.*, c.nome as colaborador_nome
        FROM alerta a
        JOIN alerta_colaborador ac ON a.id_alerta = ac.id_alerta
        JOIN colaborador c ON ac.id_colaborador = c.id_colaborador
        WHERE c.id_equipa IN (" . implode(',', array_fill(0, count($equipeIds), '?')) . ")
        AND a.status = 'Pendente'
        ORDER BY a.prioridade DESC, a.data_criacao ASC
    ");
    $stmt->bind_param(str_repeat('i', count($equipeIds)), ...$equipeIds);
    $stmt->execute();
    $dados['alertas'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $dados['alertasPendentes'] = count($dados['alertas']);
    
    // Obter documentos pendentes
    $stmt = $conn->prepare("
        SELECT d.*, c.nome as colaborador_nome
        FROM documento d
        JOIN colaborador c ON d.id_colaborador = c.id_colaborador
        WHERE c.id_equipa IN (" . implode(',', array_fill(0, count($equipeIds), '?')) . ")
        AND d.status = 'Pendente'
        ORDER BY d.data_upload DESC
    ");
    $stmt->bind_param(str_repeat('i', count($equipeIds)), ...$equipeIds);
    $stmt->execute();
    $dados['documentos'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Preparar dados para gráficos
    $dados['graficos'] = [
        'idade' => prepareIdadeData($colaboradores),
        'tempo' => prepareTempoData($colaboradores),
        'funcao' => prepareFuncaoData($colaboradores),
        'genero' => prepareGeneroData($colaboradores)
    ];
    
    return $dados;
}

// Funções auxiliares para preparar dados dos gráficos
function prepareIdadeData($colaboradores) {
    $idades = array_map(function($c) {
        return date_diff(date_create($c['data_nascimento']), date_create('now'))->y;
    }, $colaboradores);
    
    $bins = array_fill(0, 10, 0);
    foreach ($idades as $idade) {
        $bin = min(9, floor($idade / 10));
        $bins[$bin]++;
    }
    
    $labels = array_map(function($i) {
        return ($i * 10) . '-' . (($i + 1) * 10 - 1);
    }, range(0, 9));
    
    return [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Distribuição de Idade',
                'data' => $bins,
                'backgroundColor' => 'rgba(0, 71, 171, 0.5)',
                'borderColor' => 'rgba(0, 71, 171, 1)',
                'borderWidth' => 1
            ]
        ]
    ];
}

function prepareTempoData($colaboradores) {
    $tempos = array_map(function($c) {
        $diferenca = date_diff(date_create($c['data_entrada']), date_create('now'));
        return $diferenca->y + ($diferenca->m / 12);
    }, $colaboradores);
    
    sort($tempos);
    
    return [
        'labels' => array_map(function($i) {
            return date('M Y', strtotime("-$i months"));
        }, range(0, 11)),
        'datasets' => [
            [
                'label' => 'Tempo na Empresa',
                'data' => array_fill(0, 12, 0),
                'borderColor' => 'rgba(0, 71, 171, 1)',
                'backgroundColor' => 'rgba(0, 71, 171, 0.1)',
                'fill' => true
            ]
        ]
    ];
}

function prepareFuncaoData($colaboradores) {
    $funcoes = array_column($colaboradores, 'funcao_nome');
    $contagem = array_count_values($funcoes);
    
    return [
        'labels' => array_keys($contagem),
        'datasets' => [
            [
                'data' => array_values($contagem),
                'backgroundColor' => [
                    'rgba(0, 71, 171, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ]
            ]
        ]
    ];
}

function prepareGeneroData($colaboradores) {
    $generos = array_column($colaboradores, 'genero');
    $contagem = array_count_values($generos);
    
    return [
        'labels' => array_keys($contagem),
        'datasets' => [
            [
                'data' => array_values($contagem),
                'backgroundColor' => [
                    'rgba(0, 71, 171, 0.7)',
                    'rgba(255, 193, 7, 0.7)'
                ]
            ]
        ]
    ];
}

// Função para marcar alerta como lido
function marcarAlertaComoLido($alertaId, $colaboradorId) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE alerta_colaborador
        SET data_leitura = NOW(), lido = TRUE
        WHERE id_alerta = ? AND id_colaborador = ?
    ");
    $stmt->bind_param("ii", $alertaId, $colaboradorId);
    return $stmt->execute();
}

// Função para obter detalhes do alerta
function getAlertaDetalhes($alertaId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT a.*, c.nome as colaborador_nome
        FROM alerta a
        JOIN alerta_colaborador ac ON a.id_alerta = ac.id_alerta
        JOIN colaborador c ON ac.id_colaborador = c.id_colaborador
        WHERE a.id_alerta = ?
    ");
    $stmt->bind_param("i", $alertaId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? null;
}

// Função para obter detalhes do documento
function getDocumentoDetalhes($documentoId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT d.*, c.nome as colaborador_nome
        FROM documento d
        JOIN colaborador c ON d.id_colaborador = c.id_colaborador
        WHERE d.id_documento = ?
    ");
    $stmt->bind_param("i", $documentoId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? null;
}

// Funções auxiliares para calcular médias
function calculateMediaIdade($colaboradores) {
    if (empty($colaboradores)) return 0;
    
    $total = 0;
    foreach ($colaboradores as $colab) {
        $idade = date_diff(date_create($colab['data_nascimento']), date_create('now'))->y;
        $total += $idade;
    }
    return round($total / count($colaboradores));
}

function calculateMediaTempo($colaboradores) {
    if (empty($colaboradores)) return '0 meses';
    
    $totalMeses = 0;
    foreach ($colaboradores as $colab) {
        $tempo = date_diff(date_create($colab['data_entrada']), date_create('now'));
        $totalMeses += $tempo->y * 12 + $tempo->m;
    }
    $mediaMeses = round($totalMeses / count($colaboradores));
    return floor($mediaMeses / 12) . ' anos e ' . ($mediaMeses % 12) . ' meses';
}
?>
// Verifica autenticação
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}
?>
