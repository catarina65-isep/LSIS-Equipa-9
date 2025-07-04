<?php
session_start();

// Verifica se o utilizador está logado e tem perfil de RH (ID 2)
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Gráficos</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #4361ee;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .chart {
            width: 100%;
            height: 400px;
        }
        #chart3 {
            height: 500px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
            box-sizing: border-box;
        }
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 10px;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Teste de Gráficos</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Distribuição por Função</div>
                    <div class="card-body">
                        <div id="chart1" class="chart"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Distribuição por Gênero</div>
                    <div class="card-body">
                        <div id="chart2" class="chart"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Evolução da Remuneração Média</div>
                    <div class="card-body">
                        <div id="chart3" class="chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados do gráfico 1 - Barras (Distribuição por função)
        var data1 = [{
            x: ['Engenharia', 'Marketing', 'Vendas', 'RH', 'Financeiro'],
            y: [40, 25, 30, 10, 15],
            type: 'bar',
            marker: {
                color: '#4361ee'
            }
        }];

        var layout1 = {
            plot_bgcolor: 'rgba(0,0,0,0)',
            paper_bgcolor: 'rgba(0,0,0,0)',
            font: { color: '#222' },
            yaxis: { 
                title: 'Número de Colaboradores',
                gridcolor: '#f0f0f0',
                showgrid: true
            },
            xaxis: { 
                title: 'Função',
                gridcolor: '#f0f0f0',
                showgrid: false
            },
            margin: { t: 10, l: 60, r: 30, b: 60 },
            showlegend: false
        };

        // Dados do gráfico 2 - Pizza (Distribuição por género)
        var data2 = [{
            values: [55, 43, 2],
            labels: ['Masculino', 'Feminino', 'Outro'],
            type: 'pie',
            marker: {
                colors: ['#4361ee', '#4cc9f0', '#f72585']
            },
            textinfo: 'percent+label',
            textposition: 'inside',
            hoverinfo: 'label+percent',
            textfont: {
                size: 14
            }
        }];

        var layout2 = {
            plot_bgcolor: 'rgba(0,0,0,0)',
            paper_bgcolor: 'rgba(0,0,0,0)',
            font: { color: '#222' },
            showlegend: true,
            legend: {
                orientation: 'h',
                y: -0.1
            },
            margin: { t: 10, l: 30, r: 30, b: 30 }
        };

        // Dados do gráfico 3 - Linha (Evolução da remuneração média)
        var data3 = [{
            x: [2019, 2020, 2021, 2022, 2023],
            y: [30000, 32000, 35000, 37000, 40000],
            type: 'scatter',
            mode: 'lines+markers',
            line: {
                color: '#4cc9f0',
                width: 3
            },
            marker: {
                size: 8,
                color: '#4361ee'
            }
        }];

        var layout3 = {
            plot_bgcolor: 'rgba(0,0,0,0)',
            paper_bgcolor: 'rgba(0,0,0,0)',
            font: { color: '#222' },
            yaxis: { 
                title: 'Remuneração Média (€)',
                gridcolor: '#f0f0f0',
                tickprefix: '€',
                tickformat: '.2s',
                hoverformat: '€.2f',
                showgrid: true
            },
            xaxis: { 
                title: 'Ano', 
                dtick: 1,
                showgrid: false
            },
            margin: { t: 10, l: 60, r: 30, b: 60 },
            hovermode: 'closest'
        };

        // Renderizar os gráficos quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            Plotly.newPlot('chart1', data1, layout1, {responsive: true});
            Plotly.newPlot('chart2', data2, layout2, {responsive: true});
            Plotly.newPlot('chart3', data3, layout3, {responsive: true});

            // Ajustar o tamanho dos gráficos quando a janela for redimensionada
            window.addEventListener('resize', function() {
                Plotly.Plots.resize('chart1');
                Plotly.Plots.resize('chart2');
                Plotly.Plots.resize('chart3');
            });
        });
    </script>
</body>
</html>
