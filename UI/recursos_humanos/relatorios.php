<?php
session_start();
$page_title = "Relatórios RH - Tlantic";

// Função para buscar aniversariantes por equipe
function buscarAniversariantesPorEquipa() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=ficha_colaboradores', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "
            SELECT 
                IFNULL(e.nome, 'Sem Equipa') AS equipa,
                CONCAT(c.nome, ' ', COALESCE(c.apelido, '')) AS colaborador,
                c.data_nascimento,
                TIMESTAMPDIFF(YEAR, c.data_nascimento, CURDATE()) AS idade,
                CASE 
                    WHEN DAY(c.data_nascimento) = DAY(CURDATE()) 
                    AND MONTH(c.data_nascimento) = MONTH(CURDATE()) 
                    THEN '🎂 Hoje é o aniversário!'
                    ELSE CONCAT(
                        'Faltam ', 
                        DATEDIFF(
                            DATE_ADD(
                                DATE_FORMAT(CONCAT(YEAR(CURDATE()), '-', MONTH(c.data_nascimento), '-', DAY(c.data_nascimento)), '%Y-%m-%d'),
                                INTERVAL IF(DAYOFYEAR(CONCAT(YEAR(CURDATE()), '-', MONTH(c.data_nascimento), '-', DAY(c.data_nascimento))) < DAYOFYEAR(CURDATE()), 1, 0) YEAR
                            ),
                            CURDATE()
                        ),
                        ' dias'
                    )
                END AS proximo_aniversario
            FROM 
                colaborador c
            LEFT JOIN 
                equipa e ON c.id_equipa = e.id_equipa
            WHERE 
                c.estado = 'Ativo'
                AND c.data_nascimento IS NOT NULL
            ORDER BY 
                e.nome,
                MONTH(c.data_nascimento),
                DAY(c.data_nascimento)
        ";
        
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar aniversariantes: " . $e->getMessage());
        return [];
    }
}

// Função para buscar relatório de vouchers
function buscarRelatorioVouchers() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=ficha_colaboradores', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Buscar o total de vouchers disponíveis (configuração do sistema)
        $queryConfig = "SELECT valor FROM configuracao WHERE chave = 'total_vouchers_disponiveis' LIMIT 1";
        $stmtConfig = $pdo->query($queryConfig);
        $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
        $totalVouchersDisponiveis = $config ? (int)$config['valor'] : 0;
        
        // Buscar o total de vouchers já atribuídos
        $queryAtribuidos = "
            SELECT COUNT(*) as total_atribuidos 
            FROM colaborador 
            WHERE ultimo_voucher_telemovel IS NOT NULL
            AND estado = 'Ativo'
        ";
        $stmtAtribuidos = $pdo->query($queryAtribuidos);
        $totalAtribuidos = (int)$stmtAtribuidos->fetch(PDO::FETCH_ASSOC)['total_atribuidos'];
        
        // Calcular disponíveis
        $vouchersDisponiveis = $totalVouchersDisponiveis - $totalAtribuidos;
        if ($vouchersDisponiveis < 0) $vouchersDisponiveis = 0;
        
        // Buscar detalhes por equipe
        $queryEquipes = "
            SELECT 
                IFNULL(e.nome, 'Sem Equipa') AS equipa,
                COUNT(c.id_colaborador) AS total_colaboradores,
                SUM(CASE WHEN c.ultimo_voucher_telemovel IS NOT NULL THEN 1 ELSE 0 END) AS com_voucher,
                MAX(DATE_FORMAT(c.ultimo_voucher_telemovel, '%d/%m/%Y')) as ultima_atribuicao
            FROM 
                colaborador c
            LEFT JOIN 
                equipa e ON c.id_equipa = e.id_equipa
            WHERE 
                c.estado = 'Ativo'
            GROUP BY 
                e.nome
            ORDER BY 
                e.nome
        ";
        
        $stmtEquipes = $pdo->query($queryEquipes);
        $dadosEquipes = $stmtEquipes->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'total_geral' => [
                'total_vouchers' => $totalVouchersDisponiveis,
                'total_atribuidos' => $totalAtribuidos,
                'total_disponiveis' => $vouchersDisponiveis
            ],
            'por_equipe' => $dadosEquipes
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar relatório de vouchers: " . $e->getMessage());
        return [
            'total_geral' => [
                'total_vouchers' => 0,
                'total_atribuidos' => 0,
                'total_disponiveis' => 0
            ],
            'por_equipe' => []
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2533 100%);
            color: #fff;
            width: var(--sidebar-width);
            position: fixed;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            margin: 5px 15px;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
            font-weight: 400;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
        }
        
        .sidebar .nav-link.active i {
            color: white;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
        }
        
        .sidebar-header h4 {
            color: white;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .sidebar-header h4 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #2c3e50;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 25px;
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-top: none;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            border-radius: 20px;
        }
        
        .nav-pills .nav-link {
            color: #495057;
            border-radius: 8px;
            margin-right: 5px;
            padding: 8px 15px;
            transition: all 0.2s;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            font-weight: 500;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- Incluir a barra lateral -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-3" id="sidebarToggle">
                    <i class='bx bx-menu'></i>
                </button>
                <h2 class="page-title mb-0">Relatórios</h2>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3 d-none d-md-inline">
                    <i class='bx bx-calendar'></i> <?= date('d/m/Y') ?>
                </span>
            </div>
        </div>
                
                <ul class="nav nav-pills mb-4" id="relatoriosTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="aniversarios-tab" data-bs-toggle="pill" data-bs-target="#aniversarios" type="button" role="tab">
                            <i class='bx bx-cake me-1'></i> Aniversários por Equipa
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="vouchers-tab" data-bs-toggle="pill" data-bs-target="#vouchers" type="button" role="tab">
                            <i class='bx bx-mobile-alt me-1'></i> Vouchers de Telemóvel
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="relatoriosTabContent">
                    <!-- Aba de Aniversários -->
                    <div class="tab-pane fade show active" id="aniversarios" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Aniversários por Equipa</h5>
                                <button class="btn btn-sm btn-primary" onclick="window.print()">
                                    <i class='bx bx-printer me-1'></i> Imprimir
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tabelaAniversarios">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Equipa</th>
                                                <th>Colaborador</th>
                                                <th>Data Nascimento</th>
                                                <th>Idade</th>
                                                <th>Próximo Aniversário</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $aniversariantes = buscarAniversariantesPorEquipa();
                                            $equipe_atual = '';
                                            
                                            foreach ($aniversariantes as $item): 
                                                $is_nova_equipe = ($equipe_atual != $item['equipa']);
                                                $equipe_atual = $item['equipa'];
                                                
                                                if ($is_nova_equipe): 
                                            ?>
                                            <tr class="table-active">
                                                <td colspan="5" class="fw-bold">
                                                    <i class='bx bx-group me-2'></i><?= htmlspecialchars($equipe_atual) ?>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                            
                                            <tr class="<?= strpos($item['proximo_aniversario'], 'Hoje') !== false ? 'table-success' : '' ?>">
                                                <td></td>
                                                <td><?= htmlspecialchars($item['colaborador']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($item['data_nascimento'])) ?></td>
                                                <td><?= $item['idade'] ?> anos</td>
                                                <td><?= $item['proximo_aniversario'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if (empty($aniversariantes)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="alert alert-info mb-0">
                                                        Nenhum aniversariante encontrado.
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-end">
                                <small>Atualizado em: <?= date('d/m/Y H:i:s') ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba de Vouchers -->
                    <div class="tab-pane fade" id="vouchers" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Relatório de Vouchers de Telemóvel</h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#modalConfigVouchers">
                                        <i class='bx bx-cog me-1'></i> Configurar
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="window.print()">
                                        <i class='bx bx-printer me-1'></i> Imprimir
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php 
                                $dadosVouchers = buscarRelatorioVouchers();
                                $totalGeral = $dadosVouchers['total_geral'];
                                $porEquipe = $dadosVouchers['por_equipe'];
                                ?>
                                
                                <!-- Resumo Geral -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted mb-1">Total de Vouchers</h6>
                                                <h3 class="text-primary"><?= $totalGeral['total_vouchers'] ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted mb-1">Vouchers Atribuídos</h6>
                                                <h3 class="text-success"><?= $totalGeral['total_atribuidos'] ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted mb-1">Vouchers Disponíveis</h6>
                                                <h3 class="text-<?= $totalGeral['total_disponiveis'] > 0 ? 'primary' : 'danger' ?>">
                                                    <?= $totalGeral['total_disponiveis'] ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tabela por Equipe -->
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tabelaVouchers">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Equipa</th>
                                                <th>Total Colaboradores</th>
                                                <th>Com Voucher</th>
                                                <th>Sem Voucher</th>
                                                <th>Última Atribuição</th>
                                                <th>% Cobertura</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            if (!empty($porEquipe)): 
                                                foreach ($porEquipe as $equipe): 
                                                    $percentual = $equipe['total_colaboradores'] > 0 
                                                        ? round(($equipe['com_voucher'] / $equipe['total_colaboradores']) * 100, 1)
                                                        : 0;
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($equipe['equipa']) ?></td>
                                                    <td><?= $equipe['total_colaboradores'] ?></td>
                                                    <td>
                                                        <span class="badge bg-success"><?= $equipe['com_voucher'] ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $equipe['total_colaboradores'] - $equipe['com_voucher'] > 0 ? 'warning' : 'light' ?>">
                                                            <?= $equipe['total_colaboradores'] - $equipe['com_voucher'] ?>
                                                        </span>
                                                    </td>
                                                    <td><?= $equipe['ultima_atribuicao'] ?? 'Nunca' ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $percentual >= 90 ? 'success' : ($percentual >= 50 ? 'info' : 'warning') ?>" 
                                                                 role="progressbar" 
                                                                 style="width: <?= $percentual ?>%" 
                                                                 aria-valuenow="<?= $percentual ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                <?= $percentual ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endforeach; 
                                                
                                                // Adicionar linha de total geral
                                                $totalColaboradores = array_sum(array_column($porEquipe, 'total_colaboradores'));
                                                $totalComVoucher = array_sum(array_column($porEquipe, 'com_voucher'));
                                                $percentualGeral = $totalColaboradores > 0 
                                                    ? round(($totalComVoucher / $totalColaboradores) * 100, 1) 
                                                    : 0;
                                            ?>
                                                <tr class="table-active fw-bold">
                                                    <td>Total Geral</td>
                                                    <td><?= $totalColaboradores ?></td>
                                                    <td><span class="badge bg-success"><?= $totalComVoucher ?></span></td>
                                                    <td><span class="badge bg-<?= ($totalColaboradores - $totalComVoucher) > 0 ? 'warning' : 'light' ?>"><?= $totalColaboradores - $totalComVoucher ?></span></td>
                                                    <td>-</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $percentualGeral >= 90 ? 'success' : ($percentualGeral >= 50 ? 'info' : 'warning') ?>" 
                                                                 role="progressbar" 
                                                                 style="width: <?= $percentualGeral ?>%" 
                                                                 aria-valuenow="<?= $percentualGeral ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                <?= $percentualGeral ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="alert alert-info mb-0">
                                                            Nenhum dado de voucher encontrado.
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-end">
                                <small>Atualizado em: <?= date('d/m/Y H:i:s') ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal de Configuração de Vouchers -->
                    <div class="modal fade" id="modalConfigVouchers" tabindex="-1" aria-labelledby="modalConfigVouchersLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalConfigVouchersLabel">Configurar Vouchers</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <form method="POST" action="processar_config_vouchers.php" onsubmit="return validarFormularioVouchers(this);">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="totalVouchers" class="form-label">Total de Vouchers Disponíveis</label>
                                            <input type="number" class="form-control" id="totalVouchers" name="totalVouchers" 
                                                   value="<?= $totalGeral['total_vouchers'] ?>" min="0" required>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class='bx bx-info-circle me-2'></i>
                                            O sistema enviará alertas automáticos 23 meses após a última emissão de voucher.
                                        </div>
                                        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'true'): ?>
                                            <div class="alert alert-success">
                                                <i class='bx bx-check-circle me-2'></i>
                                                Configuração salva com sucesso!
                                            </div>
                                        <?php elseif (isset($_GET['erro'])): ?>
                                            <div class="alert alert-danger">
                                                <i class='bx bx-error-circle me-2'></i>
                                                <?= htmlspecialchars(urldecode($_GET['erro'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Salvar Configuração</button>
                                    </div>
                                </form>
                                <script>
                                function validarFormularioVouchers(form) {
                                    const totalVouchers = form.totalVouchers.value.trim();
                                    if (totalVouchers === '' || isNaN(totalVouchers) || parseInt(totalVouchers) < 0) {
                                        alert('Por favor, insira um valor válido para o total de vouchers.');
                                        return false;
                                    }
                                    return true;
                                }
                                
                                // Fechar automaticamente mensagens de sucesso/erro após 5 segundos
                                document.addEventListener('DOMContentLoaded', function() {
                                    const alertas = document.querySelectorAll('.alert');
                                    alertas.forEach(function(alerta) {
                                        setTimeout(function() {
                                            alerta.style.transition = 'opacity 0.5s';
                                            alerta.style.opacity = '0';
                                            setTimeout(function() {
                                                alerta.remove();
                                            }, 500);
                                        }, 5000);
                                    });
                                });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on smaller screens
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                sidebar.style.left = '-250px';
                mainContent.style.marginLeft = '0';
                mainContent.style.width = '100%';
            } else {
                sidebar.style.left = '0';
                mainContent.style.marginLeft = '250px';
                mainContent.style.width = 'calc(100% - 250px)';
            }
        }

        // Initialize and add resize listener
        window.addEventListener('DOMContentLoaded', () => {
            toggleSidebar();
            window.addEventListener('resize', toggleSidebar);
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#tabelaAniversarios, #tabelaVouchers').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json'
                },
                order: [],
                pageLength: 25,
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                columnDefs: [
                    { orderable: false, targets: [0, 4] } // Desabilitar ordenação nas colunas de equipe e ações
                ]
            });
            
            // Atualizar a contagem de itens nas abas
            function atualizarContagem() {
                const totalAniversarios = <?= count($aniversariantes) ?>;
                const totalVouchers = <?= count($vouchers) ?>;
                
                if (totalAniversarios > 0) {
                    $('#aniversarios-tab').append(`<span class="badge bg-primary ms-2">${totalAniversarios}</span>`);
                }
                
                if (totalVouchers > 0) {
                    $('#vouchers-tab').append(`<span class="badge bg-primary ms-2">${totalVouchers} equipes</span>`);
                }
            }
            
            atualizarContagem();
        });
    </script>
</body>
</html>