<?php
// Script temporário para aplicar todas as alterações

// 1. Atualizar CSS
$css = <<<CSS
/* Estilos gerais */
:root {
    --primary-color: #212529;
    --secondary-color: #343a40;
    --success-color: #198754;
    --danger-color: #e74c3c;
    --warning-color: #f1c40f;
    --info-color: #0dcaf0;
    --white: #ffffff;
    --light: #f8f9fa;
}

/* Container principal */
.container-fluid {
    min-height: 100vh;
    display: flex;
    flex-direction: row;
    padding-left: 250px;
}

/* Sidebar */
.sidebar {
    width: 250px;
    min-height: 100vh;
    background-color: var(--white);
    color: var(--primary-color);
    padding: 1.5rem 1rem;
    border-right: 1px solid #dee2e6;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
}

.sidebar .nav-link {
    color: var(--primary-color);
    font-weight: 500;
    padding: 0.5rem 1rem;
    margin: 0.25rem 0;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}

.sidebar .nav-link:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.sidebar .nav-link.active {
    background-color: rgba(0, 0, 0, 0.1);
}

/* Main Content */
main {
    width: calc(100% - 250px);
    margin-left: 250px;
    padding: 2rem;
    padding-left: 2rem;
}

/* Estilos para os botões de exportação */
.export-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.btn-export {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
}

.btn-export i {
    width: 1.25rem;
    text-align: center;
}

/* Responsividade */
@media (max-width: 991.98px) {
    .sidebar {
        width: 100px;
    }

    .sidebar h3 {
        display: none;
    }

    .sidebar .nav-link {
        padding: 0.5rem 0.75rem;
        text-align: center;
    }

    .sidebar .nav-link i {
        margin-right: 0;
    }

    main {
        width: calc(100% - 100px);
        margin-left: 100px;
    }
}
CSS;

// 2. Atualizar HTML
$html = <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Humanos - Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="style-rh.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="sidebar">
            <div class="logo-container">
                <img src="images/logo-tlantic.png" alt="Tlantic" class="logo">
            </div>
            <h3>Recursos Humanos</h3>
            <nav class="nav flex-column">
                <a class="nav-link active" href="#dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="#colaboradores">
                    <i class="bi bi-people"></i> Colaboradores
                </a>
                <a class="nav-link" href="#relatorios">
                    <i class="bi bi-file-earmark-text"></i> Relatórios
                </a>
                <a class="nav-link" href="#configuracoes">
                    <i class="bi bi-gear"></i> Configurações
                </a>
            </nav>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="export-buttons">
                        <button class="btn btn-success btn-export" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                        <button class="btn btn-primary btn-export" onclick="printDashboard()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Colaboradores</h5>
                            <h2 class="card-text" id="totalCollaborators">0</h2>
                        </div>
                    </div>
                </div>
                <!-- ... outros cards do dashboard ... -->
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rh.js"></script>
</body>
</html>
HTML;

// 3. Atualizar JavaScript
$js = <<<JS
// Função para exportar para Excel
function exportToExcel() {
    const link = document.createElement('a');
    link.href = '/api/export/excel.php';
    link.download = 'dashboard.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Função para imprimir o dashboard
function printDashboard() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Dashboard RH - Tlantic</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    body * {
                        visibility: hidden;
                    }
                    .print-content, .print-content * {
                        visibility: visible;
                    }
                    .print-content {
                        position: absolute;
                        left: 0;
                        top: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-content">
                <h2 class="text-center mb-4">Dashboard RH - Tlantic</h2>
                ${document.querySelector('.row').innerHTML}
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                }
            </script>
        </body>
        </html>
    `);
}

// Outras funções existentes...
JS;

// Salvar os arquivos
file_put_contents('style-rh.css', $css);
file_put_contents('rh.php', $html);
file_put_contents('js/rh.js', $js);

// Remover o script temporário
unlink(__FILE__);
?>
