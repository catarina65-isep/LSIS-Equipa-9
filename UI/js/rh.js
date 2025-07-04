// Event Listeners
window.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadFilters();
    loadEmployees();
    
    // Eventos para filtros
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', filterEmployees);
    });
    
    // Evento para pesquisa
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(searchEmployees, 300));
    }
});

// Função para exportar para Excel
function exportToExcel() {
    const link = document.createElement('a');
    link.href = '/LSIS-Equipa-9/api/export/excel.php';
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
                    body {
                        background-color: white !important;
                    }
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
                        background-color: white;
                    }
                    .card {
                        background-color: white !important;
                        border: none;
                        box-shadow: none;
                    }
                }
                body {
                    background-color: white;
                }
                .print-content {
                    background-color: white;
                    padding: 2rem;
                }
                .card {
                    background-color: white;
                    border: none;
                    box-shadow: none;
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

// Função para carregar dados do dashboard
function loadDashboardData() {
    fetch('/LSIS-Equipa-9/api/dashboard.php')
        .then(response => response.json())
        .then(data => {
            // Atualizar cards
            document.getElementById('totalCollaborators').textContent = data.totalCollaborators;
            document.getElementById('birthdays').textContent = data.birthdays;
            document.getElementById('pendingAlerts').textContent = data.pendingAlerts;
            document.getElementById('averageAge').textContent = data.averageAge;
            document.getElementById('averageTimeInCompany').textContent = data.averageTimeInCompany.toFixed(1);
            document.getElementById('retentionRate').textContent = data.retentionRate;

            // Criar gráficos
            createGenderDistributionChart(data.genderDistribution);
            createHierarchyDistributionChart(data.hierarchyDistribution);
        })
        .catch(error => console.error('Erro ao carregar dados do dashboard:', error));
}

// Função para criar gráfico de distribuição por gênero
function createGenderDistributionChart(data) {
    const ctx = document.getElementById('genderDistributionChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: [data.male, data.female],
                backgroundColor: ['#4361ee', '#ff4d6d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Função para criar gráfico de distribuição hierárquica
function createHierarchyDistributionChart(data) {
    const ctx = document.getElementById('hierarchyDistributionChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Júnior', 'Sênior', 'Gerente', 'Diretor'],
            datasets: [{
                data: [data.junior, data.senior, data.manager, data.director],
                backgroundColor: ['#4361ee', '#ff4d6d', '#2ed573', '#ffa502']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Função para gerar relatório
function generateReport() {
    const filters = {
        department: document.getElementById('departmentFilter').value,
        function: document.getElementById('functionFilter').value,
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value
    };

    fetch('/LSIS-Equipa-9/api/reports/generate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(filters)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Relatório gerado com sucesso!');
            loadEmployees();
        } else {
            alert('Erro ao gerar relatório');
        }
    })
    .catch(error => console.error('Erro ao gerar relatório:', error));
}

// Função para carregar filtros
function loadFilters() {
    fetch('/LSIS-Equipa-9/api/filters.php')
        .then(response => response.json())
        .then(data => {
            populateFilters(data);
        })
        .catch(error => console.error('Erro ao carregar filtros:', error));
}

// Função para carregar colaboradores
function loadEmployees() {
    fetch('/LSIS-Equipa-9/api/employees.php')
        .then(response => response.json())
        .then(data => {
            populateEmployeesTable(data);
        })
        .catch(error => console.error('Erro ao carregar colaboradores:', error));
}

// Função para filtrar colaboradores
function filterEmployees() {
    const filters = {
        department: document.getElementById('departmentFilter').value,
        role: document.getElementById('roleFilter').value,
        status: document.getElementById('statusFilter').value
    };
    
    fetch('/LSIS-Equipa-9/api/employees.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(filters)
    })
    .then(response => response.json())
    .then(data => {
        populateEmployeesTable(data);
    })
    .catch(error => console.error('Erro ao filtrar colaboradores:', error));
}

// Função para pesquisar colaboradores
function searchEmployees() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const employees = document.querySelectorAll('.employee-row');
    
    employees.forEach(employee => {
        const name = employee.querySelector('.employee-name').textContent.toLowerCase();
        const department = employee.querySelector('.employee-department').textContent.toLowerCase();
        employee.style.display = (name.includes(searchTerm) || department.includes(searchTerm)) ? '' : 'none';
    });
}

// Função debounce para pesquisa
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
