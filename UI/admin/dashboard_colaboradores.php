<?php
session_start();

try {
    // Incluir arquivos necessários
    require_once __DIR__ . '/../../BLL/ColaboradorBLL.php';
    require_once __DIR__ . '/../../BLL/LoginBLL.php';

    // Verificar autenticação e perfil
    $loginBLL = new LoginBLL();
    if (!$loginBLL->verificarAutenticacao() || !isset($_SESSION['id_perfilacesso']) || $_SESSION['id_perfilacesso'] != 1) {
        header('Location: ../login.php');
        exit;
    }

    $colaboradorBLL = new ColaboradorBLL();
    
    // Obter dados de colaboradores ativos com tratamento de erro
    $colaboradores = [];
    $erro = null;
    
    try {
        $colaboradores = $colaboradorBLL->obterColaboradoresAtivos();
    } catch (Exception $e) {
        error_log("Erro ao obter colaboradores: " . $e->getMessage());
        $erro = "Não foi possível carregar os dados dos colaboradores. Por favor, tente novamente mais tarde.";
    }

    $page_title = "Dashboard de Colaboradores - Admin";

} catch (Exception $e) {
    error_log("Erro no dashboard de colaboradores: " . $e->getMessage());
    $erro = "Ocorreu um erro inesperado. Por favor, entre em contato com o suporte.";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: var(--dark-bg);
            color: #fff;
            padding: 20px 0;
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.show {
            transform: translateX(var(--sidebar-width));
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .chart-container {
            height: 300px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Incluir a barra lateral -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (isset($erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class='bx bxs-error-alt me-2'></i>
            <?php echo htmlspecialchars($erro); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Dashboard de Colaboradores</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Colaboradores</li>
                </ol>
            </nav>
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>Distribuição da Idade</h3>
                <canvas id="idadeChart" class="chart-container"></canvas>
            </div>

            <div class="card">
                <h3>Distribuição por Género</h3>
                <label for="filtroGenero">Filtrar por Género:</label>
                <select id="filtroGenero">
                    <option value="Todos">Todos</option>
                </select>
                <canvas id="generoChart" class="chart-container"></canvas>
            </div>

            <div class="card">
                <h3>Distribuição por País</h3>
                <label for="filtroPais">Filtrar por País:</label>
                <select id="filtroPais">
                    <option value="Todos">Todos</option>
                </select>
                <canvas id="paisChart" class="chart-container"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Dados dos colaboradores vindo do PHP
        const colaboradores = <?php 
            if (empty($colaboradores) || !is_array($colaboradores)) {
                // Dados de exemplo para demonstração
                echo json_encode([
                    ['nome' => 'João Silva', 'data_nascimento' => '1990-05-15', 'genero' => 'Masculino', 'pais' => 'Portugal'],
                    ['nome' => 'Maria Santos', 'data_nascimento' => '1985-10-22', 'genero' => 'Feminino', 'pais' => 'Portugal'],
                    ['nome' => 'Carlos Oliveira', 'data_nascimento' => '1992-03-08', 'genero' => 'Masculino', 'pais' => 'Brasil'],
                    ['nome' => 'Ana Pereira', 'data_nascimento' => '1988-07-14', 'genero' => 'Feminino', 'pais' => 'Angola'],
                    ['nome' => 'Pedro Costa', 'data_nascimento' => '1995-12-01', 'genero' => 'Masculino', 'pais' => 'Portugal']
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode($colaboradores, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            }
        ?>;

        // Função para calcular a idade a partir da data de nascimento
        function calcularIdade(dataNascimento) {
            try {
                if (!dataNascimento) return null;
                
                const nascimento = new Date(dataNascimento);
                // Verificar se a data é válida
                if (isNaN(nascimento.getTime())) {
                    console.error('Data de nascimento inválida:', dataNascimento);
                    return null;
                }
                
                const hoje = new Date();
                let idade = hoje.getFullYear() - nascimento.getFullYear();
                const m = hoje.getMonth() - nascimento.getMonth();
                
                // Ajustar se ainda não fez aniversário este ano
                if (m < 0 || (m === 0 && hoje.getDate() < nascimento.getDate())) {
                    idade--;
                }
                
                return idade;
            } catch (e) {
                console.error('Erro ao calcular idade:', e);
                return null;
            }
        }

        // Função para exibir mensagem de erro no gráfico
        function exibirMensagemSemDados(elementId, mensagem) {
            const ctx = document.getElementById(elementId).getContext('2d');
            ctx.textAlign = 'center';
            ctx.font = '14px Arial';
            ctx.fillText(mensagem, ctx.canvas.width / 2, ctx.canvas.height / 2);
            return null;
        }

        // === Gráfico de Idades ===
        try {
            const idades = colaboradores.map(c => calcularIdade(c.data_nascimento));
            if (idades.length === 0) {
                exibirMensagemSemDados('idadeChart', 'Nenhum dado de idade disponível');
            } else {
                new Chart(document.getElementById('idadeChart'), {
                    type: 'bar',
                    data: {
                        labels: idades.map((_, i) => `Colab. ${i + 1}`),
                        datasets: [{
                            label: 'Idade dos Colaboradores',
                            data: idades,
                            backgroundColor: 'rgba(75, 192, 192, 0.7)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Colaboradores' } },
                            y: { 
                                beginAtZero: true, 
                                title: { display: true, text: 'Idade' },
                                suggestedMax: Math.max(...idades) + 5 // Adiciona um pouco de espaço no topo
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição de Idades',
                                font: { size: 16 }
                            },
                            legend: { display: false }
                        }
                    }
                });
            }
        } catch (e) {
            console.error('Erro ao criar gráfico de idades:', e);
            exibirMensagemSemDados('idadeChart', 'Erro ao carregar dados de idade');
        }

        // === Utilidades ===
        function contarPorCampo(array, campo) {
            try {
                const contagem = {};
                array.forEach(item => {
                    if (item && item[campo] !== undefined) {
                        contagem[item[campo]] = (contagem[item[campo]] || 0) + 1;
                    }
                });
                return contagem;
            } catch (e) {
                console.error(`Erro ao contar por campo '${campo}':`, e);
                return {};
            }
        }

        // Inicializar filtros
        const filtroGenero = document.getElementById('filtroGenero');
        const filtroPais = document.getElementById('paisFiltro');
        let generoChart = null, paisChart = null;

        try {
            // Obter valores únicos para gênero e país, com tratamento de erros
            const generosUnicos = [...new Set(
                colaboradores
                    .filter(c => c && c.genero)
                    .map(c => c.genero)
                    .filter(Boolean)
            )];

            const paisesUnicos = [...new Set(
                colaboradores
                    .filter(c => c && c.pais)
                    .map(c => c.pais)
                    .filter(Boolean)
            )];

            // Preencher filtro de gênero
            if (generosUnicos.length > 0) {
                generosUnicos.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g;
                    opt.textContent = g;
                    filtroGenero.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Nenhum gênero disponível';
                opt.disabled = true;
                filtroGenero.appendChild(opt);
            }

            // Preencher filtro de país
            if (paisesUnicos.length > 0) {
                paisesUnicos.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p;
                    opt.textContent = p;
                    filtroPais.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Nenhum país disponível';
                opt.disabled = true;
                filtroPais.appendChild(opt);
            }
        } catch (e) {
            console.error('Erro ao inicializar filtros:', e);
        }

        function atualizarGeneroChart(filtro) {
            try {
                // Verificar se há dados para exibir
                if (!colaboradores || colaboradores.length === 0) {
                    exibirMensagemSemDados('generoChart', 'Nenhum dado de gênero disponível');
                    return;
                }

                // Filtrar dados conforme o filtro
                const dados = filtro === "Todos" || !filtro
                    ? colaboradores.filter(c => c && c.genero)
                    : colaboradores.filter(c => c && c.genero === filtro);

                // Verificar se há dados após o filtro
                if (dados.length === 0) {
                    exibirMensagemSemDados('generoChart', `Nenhum dado para ${filtro || 'o filtro selecionado'}`);
                    return;
                }

                const contagem = contarPorCampo(dados, 'genero');
                const ctx = document.getElementById('generoChart').getContext('2d');
                
                // Destruir gráfico anterior se existir
                if (generoChart) {
                    generoChart.destroy();
                }

                // Criar novo gráfico
                generoChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(contagem),
                        datasets: [{
                            data: Object.values(contagem),
                            backgroundColor: [
                                '#36A2EB', '#FF6384', '#FFCE56', '#8e44ad', 
                                '#1abc9c', '#9b59b6', '#e74c3c', '#f39c12',
                                '#3498db', '#2ecc71', '#e67e22', '#34495e'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por Gênero',
                                font: { size: 16 }
                            },
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Erro ao atualizar gráfico de gênero:', e);
                exibirMensagemSemDados('generoChart', 'Erro ao carregar dados de gênero');
            }
        }

        function atualizarPaisChart(filtro) {
            try {
                // Verificar se há dados para exibir
                if (!colaboradores || colaboradores.length === 0) {
                    exibirMensagemSemDados('paisChart', 'Nenhum dado de país disponível');
                    return;
                }

                // Filtrar dados conforme o filtro
                const dados = filtro === "Todos" || !filtro
                    ? colaboradores.filter(c => c && c.pais)
                    : colaboradores.filter(c => c && c.pais === filtro);

                // Verificar se há dados após o filtro
                if (dados.length === 0) {
                    exibirMensagemSemDados('paisChart', `Nenhum dado para ${filtro || 'o país selecionado'}`);
                    return;
                }

                const contagem = contarPorCampo(dados, 'pais');
                const ctx = document.getElementById('paisChart').getContext('2d');
                
                // Ordenar países por quantidade (maior para menor)
                const paisesOrdenados = Object.entries(contagem)
                    .sort((a, b) => b[1] - a[1]);

                const labels = paisesOrdenados.map(([pais]) => pais);
                const valores = paisesOrdenados.map(([_, total]) => total);

                // Destruir gráfico anterior se existir
                if (paisChart) {
                    paisChart.destroy();
                }

                // Criar novo gráfico
                paisChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nº de Colaboradores',
                            data: valores,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por País',
                                font: { size: 16 }
                            },
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.parsed.x} colaborador(es)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: { 
                                    display: true, 
                                    text: 'Nº de Colaboradores',
                                    font: { weight: 'bold' }
                                },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                }
                            },
                            y: {
                                title: { 
                                    display: true, 
                                    text: 'País',
                                    font: { weight: 'bold' }
                                },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 20,
                                top: 10,
                                bottom: 10
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Erro ao atualizar gráfico de países:', e);
                exibirMensagemSemDados('paisChart', 'Erro ao carregar dados de países');
            }
        }

        // Função para verificar se um elemento existe
        function elementoExiste(id) {
            const elemento = document.getElementById(id);
            if (!elemento) {
                console.error(`Elemento com ID '${id}' não encontrado no DOM`);
                return false;
            }
            return true;
        }

        // Função para atualizar o gráfico de idades
        function atualizarIdadeChart() {
            try {
                // Verificar se há dados para exibir
                if (!colaboradores || colaboradores.length === 0) {
                    exibirMensagemSemDados('idadeChart', 'Nenhum dado de idade disponível');
                    return;
                }

                // Calcular idades
                const idades = [];
                colaboradores.forEach(colab => {
                    if (colab && colab.data_nascimento) {
                        const idade = calcularIdade(colab.data_nascimento);
                        if (!isNaN(idade)) idades.push(idade);
                    }
                });

                // Verificar se há idades válidas
                if (idades.length === 0) {
                    exibirMensagemSemDados('idadeChart', 'Nenhuma idade válida encontrada');
                    return;
                }

                const ctx = document.getElementById('idadeChart').getContext('2d');
                
                // Destruir gráfico anterior se existir
                if (window.idadeChartInstance) {
                    window.idadeChartInstance.destroy();
                }

                // Criar novo gráfico
                window.idadeChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: idades.map((_, i) => `Colab. ${i + 1}`),
                        datasets: [{
                            label: 'Idade',
                            data: idades,
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição de Idades',
                                font: { size: 16 }
                            },
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} anos`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Colaboradores',
                                    font: { weight: 'bold' }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Idade',
                                    font: { weight: 'bold' }
                                },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                suggestedMax: Math.max(...idades) + 5
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Erro ao atualizar gráfico de idades:', e);
                exibirMensagemSemDados('idadeChart', 'Erro ao carregar idades');
            }
        }

        // Função para inicializar os gráficos
        function inicializarGraficos() {
            try {
                // Verificar se os elementos necessários existem
                const elementosNecessarios = ['generoChart', 'paisChart', 'idadeChart'];
                const elementosFaltando = elementosNecessarios.filter(id => !elementoExiste(id));
                
                if (elementosFaltando.length > 0) {
                    console.error('Alguns elementos necessários não foram encontrados:', elementosFaltando);
                    return;
                }

                // Inicializar os gráficos
                if (elementoExiste('generoChart')) {
                    atualizarGeneroChart('Todos');
                }
                
                if (elementoExiste('paisChart')) {
                    atualizarPaisChart('Todos');
                }
                
                if (elementoExiste('idadeChart')) {
                    atualizarIdadeChart();
                }
                
                // Adicionar classe de carregamento concluído para estilização
                document.body.classList.add('dashboard-carregado');
                
                console.log('Gráficos inicializados com sucesso!');
            } catch (e) {
                console.error('Erro ao inicializar gráficos:', e);
            }
        }

        // Adicionar event listeners para os filtros
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Configurar filtro de gênero
                const filtroGenero = document.getElementById('filtroGenero');
                if (filtroGenero) {
                    filtroGenero.addEventListener('change', () => {
                        try {
                            atualizarGeneroChart(filtroGenero.value);
                        } catch (e) {
                            console.error('Erro ao filtrar por gênero:', e);
                        }
                    });
                } else {
                    console.warn('Elemento filtroGenero não encontrado');
                }

                // Configurar filtro de país
                const filtroPais = document.getElementById('filtroPais');
                if (filtroPais) {
                    filtroPais.addEventListener('change', () => {
                        try {
                            atualizarPaisChart(filtroPais.value);
                        } catch (e) {
                            console.error('Erro ao filtrar por país:', e);
                        }
                    });
                } else {
                    console.warn('Elemento filtroPais não encontrado');
                }

                // Inicializar gráficos após um pequeno atraso
                setTimeout(inicializarGraficos, 100);
                
            } catch (e) {
                console.error('Erro ao configurar event listeners:', e);
            }
        });

        // Adicionar estilos para o estado de carregamento
        const style = document.createElement('style');
        style.textContent = `
            /* Estilo para o estado de carregamento */
            .dashboard-carregado .card {
                opacity: 1;
                transform: translateY(0);
            }
            
            .card {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.5s ease, transform 0.5s ease;
            }
            
            /* Melhorias na responsividade */
            @media (max-width: 768px) {
                .dashboard {
                    grid-template-columns: 1fr;
                }
                
                .card {
                    margin-bottom: 20px;
                }
                
                .chart-container {
                    max-height: 300px;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
