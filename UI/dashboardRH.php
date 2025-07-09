<?php
// Team data defined in PHP with distinct values
$teamData = [
    'A' => [
        'retentionRate' => '82.0%', // Lower retention
        'averageAge' => '28.5 anos', // Younger team
        'averageTenure' => '3.0 anos', // Shorter tenure
        'averageSalary' => '2200 €', // Lower salary
        'hierarquiaData' => [60, 15, 5], // More collaborators, less coordinators/RH
        'generoData' => [55, 25], // More male, less female
        'funcaoData' => [40, 20, 10], // More developers, less analysts/gestores
        'geografiaData' => [50, 20, 10], // More Lisboa, less Porto/Coimbra
        'tempoGeneroData' => [3.5, 2.8], // Lower tenure by gender
        'remuneracaoData' => [900, 1300, 1100], // Lower remuneration
        'hierarquiaEtariaData' => [
            [6, 8, 2, 1], // Younger age distribution
            [2, 3, 4, 1],
            [0, 1, 2, 2]
        ]
    ],
    'B' => [
        'retentionRate' => '90.5%', // Higher retention
        'averageAge' => '38.0 anos', // Older team
        'averageTenure' => '6.5 anos', // Longer tenure
        'averageSalary' => '3000 €', // Higher salary
        'hierarquiaData' => [30, 30, 20], // Balanced hierarchy
        'generoData' => [40, 40], // Equal gender
        'funcaoData' => [25, 30, 25], // Balanced functions
        'geografiaData' => [30, 40, 20], // More Porto, less Lisboa/Coimbra
        'tempoGeneroData' => [6.0, 5.5], // Higher tenure by gender
        'remuneracaoData' => [1200, 1600, 1400], // Higher remuneration
        'hierarquiaEtariaData' => [
            [3, 5, 6, 4], // Mixed age distribution
            [2, 4, 5, 3],
            [1, 2, 3, 4]
        ]
    ],
    'C' => [
        'retentionRate' => '95.0%', // Highest retention
        'averageAge' => '33.2 anos', // Middle age
        'averageTenure' => '4.8 anos', // Middle tenure
        'averageSalary' => '2600 €', // Middle salary
        'hierarquiaData' => [45, 25, 10], // Moderate hierarchy
        'generoData' => [35, 45], // More female, less male
        'funcaoData' => [30, 25, 15], // Mixed functions
        'geografiaData' => [25, 35, 30], // More Coimbra, less Lisboa/Porto
        'tempoGeneroData' => [4.2, 4.0], // Moderate tenure by gender
        'remuneracaoData' => [1000, 1500, 1300], // Moderate remuneration
        'hierarquiaEtariaData' => [
            [5, 7, 3, 2], // Balanced age distribution
            [3, 4, 3, 2],
            [1, 1, 3, 3]
        ]
    ]
];

// Default team
$defaultTeam = 'A';
$currentTeam = isset($_GET['equipa']) && array_key_exists($_GET['equipa'], $teamData) ? $_GET['equipa'] : $defaultTeam;
$currentData = $teamData[$currentTeam];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Indicadores RH – Tlantic</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f9ff;
      color: #003366;
    }
    header {
      background-color: #003366;
      color: white;
      padding: 20px;
      text-align: center;
    }
    header h1 {
      margin: 0;
    }
    header h2 {
      margin-top: 10px;
      font-weight: normal;
      font-size: 1.1em;
      color: #cce6ff;
    }
    .team-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      padding: 15px;
      background-color: #e6f0ff;
      border-bottom: 2px solid #003366;
    }
    .team-buttons button {
      padding: 10px 20px;
      font-size: 1em;
      font-weight: 600;
      color: white;
      background-color: #0077cc;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .team-buttons button:hover {
      background-color: #005fa3;
    }
    .team-buttons button.active {
      background-color: #003366;
    }
    .container {
      display: flex;
      justify-content: center;
      gap: 20px;
      padding: 30px;
      padding-left: 50px;
    }
    .container.individual {
      flex-wrap: wrap;
    }
    .container.pairs {
      flex-wrap: nowrap;
      overflow-x: auto;
      justify-content: center;
    }
    .charts-row {
      display: flex;
      flex-wrap: nowrap;
      gap: 20px;
      width: 100%;
      overflow-x: auto;
      align-self: flex-start;
    }
    .chart-box {
      background-color: white;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 51, 102, 0.1);
      width: 100%;
      max-width: 300px;
      box-sizing: border-box;
      text-align: center;
      flex: 1;
      min-width: 250px;
    }
    .container.pairs .chart-group {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .container.pairs .chart-box.tempo-genero {
      max-width: 600px;
    }
    .container.pairs .chart-box.remuneracao {
      max-width: 600px;
    }
    .container.pairs .chart-box.hierarquia-etaria {
      max-width: 550px;
    }
    .compact-box {
      padding: 10px;
      max-height: 150px;
      flex: 1 1 300px;
    }
    canvas {
      width: 100% !important;
      height: 200px !important;
    }
    .container.pairs .chart-box.tempo-genero canvas {
      height: 350px !important;
    }
    .container.pairs .chart-box.remuneracao canvas {
      height: 350px !important;
    }
    .container.pairs .chart-box.hierarquia-etaria canvas {
      height: 350px !important;
    }
    h2 {
      font-size: 1.2em;
      color: #003366;
      margin-bottom: 10px;
      text-align: center;
      font-weight: 600;
    }
    .metric-value {
      font-size: 2em;
      color: #0077cc;
      margin: 5px 0;
      font-weight: normal;
    }
    .metric-legend {
      font-size: 0.9em;
      color: #005fa3;
      margin-top: 5px;
      font-weight: normal;
    }
    .section {
      margin-bottom: 40px;
    }
    .section-title {
      background-color: #003366;
      color: white;
      padding: 8px;
      border-radius: 8px;
      text-align: center;
      font-size: 1.5em;
      margin: 20px 30px 10px;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="team-buttons">
    <button onclick="switchTeam('A')" id="btnEquipaA" class="<?php echo $currentTeam === 'A' ? 'active' : ''; ?>">Equipa A</button>
    <button onclick="switchTeam('B')" id="btnEquipaB" class="<?php echo $currentTeam === 'B' ? 'active' : ''; ?>">Equipa B</button>
    <button onclick="switchTeam('C')" id="btnEquipaC" class="<?php echo $currentTeam === 'C' ? 'active' : ''; ?>">Equipa C</button>
  </div>

  <header>
    <h1>Indicadores Tlantic</h1>
    <h2 id="headerTeam">Dashboard de Recursos Humanos - Equipa <?php echo $currentTeam; ?></h2>
  </header>

  <div class="section">
    <div class="section-title">Indicadores Individuais</div>
    <div class="container individual">
      <div class="chart-box compact-box">
        <h2>Taxa de Retenção</h2>
        <div class="metric-value" id="retentionRate"><?php echo $currentData['retentionRate']; ?></div>
        <div class="metric-legend">Percentagem de colaboradores retidos na empresa</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Idade Média</h2>
        <div class="metric-value" id="averageAge"><?php echo $currentData['averageAge']; ?></div>
        <div class="metric-legend">Média de idade dos colaboradores da Tlantic</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Tempo Médio na Tlantic</h2>
        <div class="metric-value" id="averageTenure"><?php echo $currentData['averageTenure']; ?></div>
        <div class="metric-legend">Tempo médio de permanência dos colaboradores na empresa</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Remuneração Média</h2>
        <div class="metric-value" id="averageSalary"><?php echo $currentData['averageSalary']; ?></div>
        <div class="metric-legend">Remuneração média mensal dos colaboradores</div>
      </div>
      <div class="charts-row">
        <div class="chart-box">
          <h2>Distribuição por Nível Hierárquico</h2>
          <canvas id="chartHierarquia"></canvas>
        </div>
        <div class="chart-box">
          <h2>Distribuição por Género</h2>
          <canvas id="chartGenero"></canvas>
        </div>
        <div class="chart-box">
          <h2>Distribuição por Função</h2>
          <canvas id="chartFuncao"></canvas>
        </div>
        <div class="chart-box">
          <h2>Distribuição por Geografia</h2>
          <canvas id="chartGeografia"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-title">Indicadores Relacionados</div>
    <div class="container pairs">
      <div class="chart-group">
        <div class="chart-box tempo-genero">
          <h2>Tempo Médio na Empresa por Género</h2>
          <canvas id="chartTempoGenero"></canvas>
        </div>
        <div class="chart-box remuneracao">
          <h2>Remuneração por Hierarquia</h2>
          <canvas id="chartRemuneracao"></canvas>
        </div>
      </div>
      <div class="chart-box hierarquia-etaria">
        <h2>Nível Hierárquico por Faixa Etária</h2>
        <canvas id="chartHierarquiaEtaria"></canvas>
      </div>
    </div>
  </div>

  <script>
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.font.size = 14;
    Chart.defaults.font.weight = '500';
    Chart.defaults.color = '#003366';

    // Team data from PHP
    let teamData = <?php echo json_encode($teamData); ?>;
    let currentTeam = '<?php echo $currentTeam; ?>';

    let chartHierarquia, chartGenero, chartFuncao, chartGeografia, chartTempoGenero, chartRemuneracao, chartHierarquiaEtaria;

    function switchTeam(team) {
      currentTeam = team;
      updateDashboard();
      document.getElementById('headerTeam').textContent = `Dashboard de Recursos Humanos - Equipa ${currentTeam}`;
      document.getElementById('btnEquipaA').className = currentTeam === 'A' ? 'active' : '';
      document.getElementById('btnEquipaB').className = currentTeam === 'B' ? 'active' : '';
      document.getElementById('btnEquipaC').className = currentTeam === 'C' ? 'active' : '';
    }

    function updateDashboard() {
      const currentData = teamData[currentTeam];

      // Update metrics
      document.getElementById('retentionRate').textContent = currentData.retentionRate;
      document.getElementById('averageAge').textContent = currentData.averageAge;
      document.getElementById('averageTenure').textContent = currentData.averageTenure;
      document.getElementById('averageSalary').textContent = currentData.averageSalary;

      // Destroy existing charts if they exist
      if (chartHierarquia) chartHierarquia.destroy();
      if (chartGenero) chartGenero.destroy();
      if (chartFuncao) chartFuncao.destroy();
      if (chartGeografia) chartGeografia.destroy();
      if (chartTempoGenero) chartTempoGenero.destroy();
      if (chartRemuneracao) chartRemuneracao.destroy();
      if (chartHierarquiaEtaria) chartHierarquiaEtaria.destroy();

      // Chart: Distribuição por Nível Hierárquico
      chartHierarquia = new Chart(document.getElementById('chartHierarquia'), {
        type: 'line',
        data: {
          labels: ['Colaborador', 'Coordenador', 'RH'],
          datasets: [{
            label: 'Total',
            data: currentData.hierarquiaData,
            fill: true,
            backgroundColor: 'rgba(0,119,204,0.2)',
            borderColor: '#0077cc',
            tension: 0.3
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' } } } }
        }
      });

      // Chart: Distribuição por Género
      chartGenero = new Chart(document.getElementById('chartGenero'), {
        type: 'pie',
        data: {
          labels: ['Masculino', 'Feminino'],
          datasets: [{
            data: currentData.generoData,
            backgroundColor: ['#0077cc', '#80bfff']
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom', labels: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' }, color: '#003366', boxWidth: 20, padding: 10 } }
          }
        }
      });

      // Chart: Distribuição por Função
      chartFuncao = new Chart(document.getElementById('chartFuncao'), {
        type: 'bar',
        data: {
          labels: ['Desenvolvedor', 'Analista', 'Gestor'],
          datasets: [{
            data: currentData.funcaoData,
            backgroundColor: ['#003f6b', '#005fa3', '#0077cc', '#3399ff']
          }]
        },
        options: {
          indexAxis: 'y',
          plugins: { legend: { display: false } },
          scales: { x: { beginAtZero: true }, y: { ticks: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' } } } }
        }
      });

      // Chart: Distribuição por Geografia
      chartGeografia = new Chart(document.getElementById('chartGeografia'), {
        type: 'pie',
        data: {
          labels: ['Lisboa', 'Porto', 'Coimbra'],
          datasets: [{
            data: currentData.geografiaData,
            backgroundColor: ['#004c99', '#0066cc', '#3399ff', '#99ccff']
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom', labels: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' }, color: '#003366', boxWidth: 20, padding: 10 } }
          }
        }
      });

      // Chart: Tempo Médio na Empresa por Género
      chartTempoGenero = new Chart(document.getElementById('chartTempoGenero'), {
        type: 'bar',
        data: {
          labels: ['Masculino', 'Feminino'],
          datasets: [{
            data: currentData.tempoGeneroData,
            backgroundColor: ['#004c99', '#66b2ff'],
            barThickness: 50
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, title: { display: true, text: 'Anos' } } }
        }
      });

      // Chart: Remuneração por Hierarquia
      chartRemuneracao = new Chart(document.getElementById('chartRemuneracao'), {
        type: 'bar',
        data: {
          labels: ['Colaborador', 'Coordenador', 'RH'],
          datasets: [{
            label: 'Remuneração Média (€)',
            data: currentData.remuneracaoData,
            backgroundColor: ['#66b2ff', '#3399cc', '#0077cc']
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          responsive: true,
          scales: { y: { beginAtZero: true, title: { display: true, text: '€ Remuneração' } } }
        }
      });

      // Chart: Nível Hierárquico por Faixa Etária
      chartHierarquiaEtaria = new Chart(document.getElementById('chartHierarquiaEtaria'), {
        type: 'bar',
        data: {
          labels: ['<25', '25-35', '36-45', '>45'],
          datasets: [
            { label: 'Colaborador', data: currentData.hierarquiaEtariaData[0], backgroundColor: '#0066cc' },
            { label: 'Coordenador', data: currentData.hierarquiaEtariaData[1], backgroundColor: '#cc66ff' },
            { label: 'RH', data: currentData.hierarquiaEtariaData[2], backgroundColor: '#3399cc' }
          ]
        },
        options: {
          plugins: { legend: { position: 'right', labels: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' }, padding: 20 } } },
          responsive: true,
          scales: { y: { beginAtZero: true, title: { display: true, text: 'Nº de Colaboradores' }, ticks: { stepSize: 5, min: 0, max: 25 } } }
        }
      });
    }

    // Render charts on page load
    window.onload = function() {
      updateDashboard();
    };
  </script>
</body>
</html>