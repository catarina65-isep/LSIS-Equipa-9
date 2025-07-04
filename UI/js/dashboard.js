// Função para carregar dados do dashboard
async function loadDashboardData() {
    try {
        // Atualizando o caminho da API para o local correto
        const response = await fetch('/UI/api/dashboard.php');
        const data = await response.json();
        
        if (data.success) {
            // Atualizar cards
            document.getElementById('totalCollaborators').textContent = data.data.totalCollaborators;
            document.getElementById('birthdays').textContent = data.data.birthdays;
            document.getElementById('pendingAlerts').textContent = data.data.pendingAlerts;
            document.getElementById('newHires').textContent = data.data.newHires;

            // Criar gráficos
            createGenderChart(data.data.genderDistribution);
            createEvolutionChart(data.data.collaboratorsEvolution);
            createDepartmentChart(data.data.departmentDistribution);
            createHierarchyChart(data.data.hierarchyDistribution);
            createTurnoverChart(data.data.turnoverRate);

            // Preencher tabelas
            populateBirthdaysTable(data.data.birthdaysTable);
            populateAlertsTable(data.data.alertsTable);
        }
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
    }
}

// Criar gráfico de distribuição por gênero
function createGenderChart(data) {
    const chart = new CanvasJS.Chart("genderChart", {
        theme: "light2",
        title: { text: "" },
        data: [{
            type: "doughnut",
            indexLabel: "{label}: {y}%",
            dataPoints: data.map(item => ({
                label: item.label,
                y: item.value
            }))
        }]
    });
    chart.render();
}

// Criar gráfico de evolução de colaboradores
function createEvolutionChart(data) {
    const chart = new CanvasJS.Chart("evolutionChart", {
        theme: "light2",
        title: { text: "" },
        axisY: { title: "Número de Colaboradores" },
        data: [{
            type: "line",
            dataPoints: data.map(item => ({
                label: item.label,
                y: item.value
            }))
        }]
    });
    chart.render();
}

// Criar gráfico de distribuição por departamento
function createDepartmentChart(data) {
    const chart = new CanvasJS.Chart("departmentChart", {
        theme: "light2",
        title: { text: "" },
        data: [{
            type: "column",
            indexLabel: "{y}",
            dataPoints: data.map(item => ({
                label: item.label,
                y: item.value
            }))
        }]
    });
    chart.render();
}

// Criar gráfico de hierarquia
function createHierarchyChart(data) {
    const chart = new CanvasJS.Chart("hierarchyChart", {
        theme: "light2",
        title: { text: "" },
        data: [{
            type: "pie",
            indexLabel: "{label}: {y}%",
            dataPoints: data.map(item => ({
                label: item.label,
                y: item.value
            }))
        }]
    });
    chart.render();
}

// Criar gráfico de taxa de rotatividade
function createTurnoverChart(data) {
    const chart = new CanvasJS.Chart("turnoverChart", {
        theme: "light2",
        title: { text: "" },
        axisY: { title: "Taxa de Rotatividade (%)" },
        data: [{
            type: "area",
            dataPoints: data.map(item => ({
                label: item.label,
                y: item.value
            }))
        }]
    });
    chart.render();
}

// Preencher tabela de aniversariantes
function populateBirthdaysTable(data) {
    const tbody = document.getElementById('birthdaysTable');
    tbody.innerHTML = data.map(item => `
        <tr>
            <td>${item.nome}</td>
            <td>${item.data_nascimento}</td>
            <td>${item.departamento}</td>
        </tr>
    `).join('');
}

// Preencher tabela de alertas
function populateAlertsTable(data) {
    const tbody = document.getElementById('alertsTable');
    tbody.innerHTML = data.map(item => `
        <tr>
            <td>${item.titulo}</td>
            <td>${item.colaborador}</td>
            <td>${item.tipo}</td>
            <td>${item.prioridade}</td>
            <td>${new Date(item.data_expiracao).toLocaleDateString()}</td>
            <td>${item.departamento}</td>
        </tr>
    `).join('');
}

// Inicializar dashboard quando a página carregar
document.addEventListener('DOMContentLoaded', loadDashboardData);
