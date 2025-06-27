<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style-rh.css">
    <style>
        /* Estilos específicos para relatórios */
        .reports-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .dashboard-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card h4 {
            color: #004d99;
            margin-bottom: 1rem;
        }

        .report-filter {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .report-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .report-table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .report-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .report-actions button {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="reports-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Relatórios e Dashboard</h2>
                        <div class="report-actions">
                            <button class="btn btn-primary" onclick="exportToExcel()">
                                <i class="bi bi-file-excel"></i> Exportar para Excel
                            </button>
                            <button class="btn btn-secondary" onclick="printReport()">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="report-filter">
                        <form id="reportFilterForm">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="startDate" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="endDate" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="department" class="form-label">Departamento</label>
                                    <select class="form-select" id="department">
                                        <option value="">Todos</option>
                                        <!-- Serão carregados via AJAX -->
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status">
                                        <option value="">Todos</option>
                                        <option value="pendente">Pendente</option>
                                        <option value="concluido">Concluído</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" onclick="loadReports()">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                        <i class="bi bi-arrow-counterclockwise"></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Dashboard -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <h4>Total de Pedidos</h4>
                                <div id="totalRequests" class="display-4">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <h4>Pedidos Pendentes</h4>
                                <div id="pendingRequests" class="display-4">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <h4>Pedidos Concluídos</h4>
                                <div id="completedRequests" class="display-4">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dashboard-card">
                                <h4>Tempo Médio</h4>
                                <div id="averageTime" class="display-4">0d</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Relatórios -->
                    <div class="report-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Departamento</th>
                                    <th>Tipo de Pedido</th>
                                    <th>Status</th>
                                    <th>Tempo de Resolução</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Serão carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Carregar departamentos
            $.get('api/departments.php', function(data) {
                const select = $('#department');
                data.forEach(dept => {
                    select.append(`<option value="${dept.id}">${dept.nome}</option>`);
                });
            });

            // Carregar dashboard inicial
            loadDashboard();

            // Carregar relatórios iniciais
            loadReports();
        });

        function loadDashboard() {
            $.get('api/dashboard.php', function(data) {
                $('#totalRequests').text(data.total_requests);
                $('#pendingRequests').text(data.pending_requests);
                $('#completedRequests').text(data.completed_requests);
                $('#averageTime').text(data.average_time + 'd');
            });
        }

        function loadReports() {
            const params = {
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
                department: $('#department').val(),
                status: $('#status').val()
            };

            $.get('api/reports.php', params, function(data) {
                const tbody = $('#reportTableBody');
                tbody.empty();

                data.forEach(report => {
                    const row = `
                        <tr>
                            <td>${report.data}</td>
                            <td>${report.departamento}</td>
                            <td>${report.tipo_pedido}</td>
                            <td>
                                <span class="badge bg-${report.status === 'concluido' ? 'success' : 'warning'}">
                                    ${report.status === 'concluido' ? 'Concluído' : 'Pendente'}
                                </span>
                            </td>
                            <td>${report.tempo_resolucao}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewDetails('${report.id}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            });
        }

        function resetFilters() {
            $('#reportFilterForm')[0].reset();
            loadReports();
        }

        function exportToExcel() {
            // Implementar exportação para Excel
            alert('Funcionalidade de exportação para Excel em desenvolvimento');
        }

        function printReport() {
            window.print();
        }

        function viewDetails(id) {
            // Implementar visualização de detalhes
            alert('Funcionalidade de visualização de detalhes em desenvolvimento');
        }
    </script>
</body>
</html>
