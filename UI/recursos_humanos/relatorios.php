<?php
session_start();

// Desativar temporariamente a verificação de login para testes
// session_start();

// Verifica se o usuário está logado e tem permissão
// if (!isset($_SESSION['usuario_id'])) {
//     header('Location: /LSIS-Equipa-9/UI/login.php');
//     exit;
// }

$page_title = "Relatórios - Tlantic";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        
        .report-card {
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }
        
        .report-card:hover {
            transform: translateX(5px);
        }
        
        .report-card i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .report-card h5 {
            color: #495057;
            margin-bottom: 10px;
        }
        
        .report-card p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .form-control, .form-select, .form-check-input {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include __DIR__ . '/includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm rounded">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Relatórios</h1>
                        <p class="mb-0 text-muted">Acesse e gere relatórios detalhados</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="gerarRelatorioBtn" disabled>
                            <i class='bx bxs-file-export'></i> Gerar Relatório
                        </button>
                    </div>
                </div>

                <div class="container-fluid px-4">
                    <div class="row g-4">
                        <!-- Filtros -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Filtros do Relatório</h5>
                                </div>
                                <div class="card-body">
                                    <form id="filtroRelatorioForm">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="tipoRelatorio" class="form-label">Tipo de Relatório <span class="text-danger">*</span></label>
                                                <select class="form-select" id="tipoRelatorio" required>
                                                    <option value="" selected disabled>Selecione um relatório...</option>
                                                    <option value="colaboradores_ativos">Colaboradores Ativos</option>
                                                    <option value="admissoes_demitidos">Admissões e Demissões</option>
                                                    <option value="ferias">Férias Programadas</option>
                                                    <option value="aniversariantes">Aniversariantes do Mês</option>
                                                    <option value="documentos_vencer">Documentos a Vencer</option>
                                                    <option value="folha_pagamento">Folha de Pagamento</option>
                                                    <option value="avaliacao_desempenho">Avaliação de Desempenho</option>
                                                    <option value="treinamentos">Treinamentos Realizados</option>
                                                    <option value="turnover">Índice de Turnover</option>
                                                    <option value="customizado">Relatório Personalizado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="periodoInicio" class="form-label">Período Inicial</label>
                                                <input type="date" class="form-control" id="periodoInicio">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="periodoFim" class="form-label">Período Final</label>
                                                <input type="date" class="form-control" id="periodoFim">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="departamento" class="form-label">Departamento</label>
                                                <select class="form-select" id="departamento">
                                                    <option value="" selected>Todos os Departamentos</option>
                                                    <option value="1">Administrativo</option>
                                                    <option value="2">Financeiro</option>
                                                    <option value="3">Recursos Humanos</option>
                                                    <option value="4">Tecnologia da Informação</option>
                                                    <option value="5">Vendas</option>
                                                    <option value="6">Marketing</option>
                                                    <option value="7">Operações</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cargo" class="form-label">Cargo</label>
                                                <select class="form-select" id="cargo">
                                                    <option value="" selected>Todos os Cargos</option>
                                                    <option value="1">Analista</option>
                                                    <option value="2">Assistente</option>
                                                    <option value="3">Coordenador</option>
                                                    <option value="4">Diretor</option>
                                                    <option value="5">Estagiário</option>
                                                    <option value="6">Gerente</option>
                                                    <option value="7">Supervisor</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="agruparDados">
                                                    <label class="form-check-label" for="agruparDados">
                                                        Agrupar dados por departamento
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Relatórios Rápidos -->
                        <div class="col-12">
                            <h5 class="mb-3">Relatórios Rápidos</h5>
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-3">
                                    <div class="card report-card h-100">
                                        <div class="card-body text-center">
                                            <i class='bx bx-user-check'></i>
                                            <h5>Colaboradores Ativos</h5>
                                            <p>Lista completa de colaboradores ativos na empresa</p>
                                            <button class="btn btn-sm btn-outline-primary mt-2" data-report="colaboradores_ativos">
                                                Gerar <i class='bx bx-chevron-right'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card report-card h-100">
                                        <div class="card-body text-center">
                                            <i class='bx bx-calendar-event text-success'></i>
                                            <h5>Aniversariantes</h5>
                                            <p>Aniversariantes do mês ou período selecionado</p>
                                            <button class="btn btn-sm btn-outline-success mt-2" data-report="aniversariantes">
                                                Gerar <i class='bx bx-chevron-right'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card report-card h-100">
                                        <div class="card-body text-center">
                                            <i class='bx bx-file text-warning'></i>
                                            <h5>Documentos</h5>
                                            <p>Documentos a vencer ou vencidos</p>
                                            <button class="btn btn-sm btn-outline-warning mt-2" data-report="documentos">
                                                Gerar <i class='bx bx-chevron-right'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card report-card h-100">
                                        <div class="card-body text-center">
                                            <i class='bx bx-line-chart text-info'></i>
                                            <h5>Turnover</h5>
                                            <p>Índice de rotatividade de pessoal</p>
                                            <button class="btn btn-sm btn-outline-info mt-2" data-report="turnover">
                                                Gerar <i class='bx bx-chevron-right'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Relatórios Salvos -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Meus Relatórios Salvos</h5>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#salvarRelatorioModal">
                                        <i class='bx bx-save'></i> Salvar Configuração
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="tabelaRelatorios">
                                            <thead>
                                                <tr>
                                                    <th>Nome do Relatório</th>
                                                    <th>Tipo</th>
                                                    <th>Data de Criação</th>
                                                    <th>Última Execução</th>
                                                    <th class="text-end">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Colaboradores Ativos - TI</td>
                                                    <td><span class="badge bg-primary">Colaboradores</span></td>
                                                    <td>15/06/2023</td>
                                                    <td>Hoje, 10:30</td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-primary me-1" title="Executar">
                                                            <i class='bx bx-play-circle'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                                            <i class='bx bx-edit-alt'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" title="Excluir">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Férias 2º Semestre</td>
                                                    <td><span class="badge bg-success">Férias</span></td>
                                                    <td>01/06/2023</td>
                                                    <td>Ontem, 14:45</td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-primary me-1">
                                                            <i class='bx bx-play-circle'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary me-1">
                                                            <i class='bx bx-edit-alt'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Documentos a Vencer</td>
                                                    <td><span class="badge bg-warning">Documentos</span></td>
                                                    <td>20/05/2023</td>
                                                    <td>Semana passada</td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-primary me-1">
                                                            <i class='bx bx-play-circle'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary me-1">
                                                            <i class='bx bx-edit-alt'></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer mt-auto py-3 bg-light">
                    <div class="container-fluid px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                &copy; <?= date('Y') ?> Tlantic - Todos os direitos reservados
                            </div>
                            <div>
                                <span class="text-muted">Versão 1.0.0</span>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Modal Salvar Relatório -->
    <div class="modal fade" id="salvarRelatorioModal" tabindex="-1" aria-labelledby="salvarRelatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salvarRelatorioModalLabel">Salvar Configuração do Relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formSalvarRelatorio">
                        <div class="mb-3">
                            <label for="nomeRelatorio" class="form-label">Nome do Relatório <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomeRelatorio" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricaoRelatorio" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricaoRelatorio" rows="3"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="compartilharRelatorio">
                            <label class="form-check-label" for="compartilharRelatorio">
                                Compartilhar com outros usuários
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formSalvarRelatorio" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicialização do DataTable
            $('#tabelaRelatorios').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json'
                },
                responsive: true,
                order: [[2, 'desc']]
            });

            // Habilitar/desabilitar botão de gerar relatório
            $('#tipoRelatorio').change(function() {
                if ($(this).val()) {
                    $('#gerarRelatorioBtn').prop('disabled', false);
                } else {
                    $('#gerarRelatorioBtn').prop('disabled', true);
                }
            });

            // Preencher data final com data atual
            const hoje = new Date().toISOString().split('T')[0];
            $('#periodoFim').val(hoje);

            // Preencher data inicial com 30 dias atrás
            const trintaDiasAtras = new Date();
            trintaDiasAtras.setDate(trintaDiasAtras.getDate() - 30);
            $('#periodoInicio').val(trintaDiasAtras.toISOString().split('T')[0]);

            // Ação para botões de relatório rápido
            $('[data-report]').click(function() {
                const reportType = $(this).data('report');
                $('#tipoRelatorio').val(reportType).trigger('change');
                
                // Rolar até o topo do formulário
                $('html, body').animate({
                    scrollTop: $('#filtroRelatorioForm').offset().top - 20
                }, 500);
                
                // Exemplo de como você poderia preencher automaticamente alguns campos
                // baseado no tipo de relatório selecionado
                switch(reportType) {
                    case 'aniversariantes':
                        const hoje = new Date();
                        const mesAtual = hoje.getMonth() + 1;
                        const anoAtual = hoje.getFullYear();
                        const diasNoMes = new Date(anoAtual, mesAtual, 0).getDate();
                        
                        $('#periodoInicio').val(`${anoAtual}-${String(mesAtual).padStart(2, '0')}-01`);
                        $('#periodoFim').val(`${anoAtual}-${String(mesAtual).padStart(2, '0')}-${diasNoMes}`);
                        break;
                    case 'documentos':
                        const dataFutura = new Date();
                        dataFutura.setDate(dataFutura.getDate() + 30); // Próximos 30 dias
                        $('#periodoFim').val(dataFutura.toISOString().split('T')[0]);
                        break;
                }
            });

            // Validação do formulário de salvar relatório
            $('#formSalvarRelatorio').on('submit', function(e) {
                e.preventDefault();
                // Lógica para salvar a configuração do relatório
                alert('Configuração do relatório salva com sucesso!');
                $('#salvarRelatorioModal').modal('hide');
                // Aqui você poderia atualizar a tabela de relatórios salvos
            });

            // Ação para o botão de gerar relatório
            $('#gerarRelatorioBtn').click(function() {
                const tipoRelatorio = $('#tipoRelatorio').val();
                const periodoInicio = $('#periodoInicio').val();
                const periodoFim = $('#periodoFim').val();
                const departamento = $('#departamento').val();
                const cargo = $('#cargo').val();
                
                // Aqui você faria uma requisição AJAX para gerar o relatório
                // ou redirecionaria para uma página de visualização do relatório
                console.log('Gerando relatório:', {
                    tipo: tipoRelatorio,
                    periodoInicio,
                    periodoFim,
                    departamento,
                    cargo
                });
                
                // Simulando geração de relatório
                alert(`Relatório "${$('#tipoRelatorio option:selected').text()}" será gerado com os filtros selecionados.`);
                
                // Em um cenário real, você poderia abrir uma nova aba/janela com o relatório
                // window.open(`gerar_relatorio.php?tipo=${tipoRelatorio}&inicio=${periodoInicio}...`, '_blank');
            });
        });
    </script>
</body>
</html>
