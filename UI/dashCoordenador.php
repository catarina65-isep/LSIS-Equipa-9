<?php
// Configuração da ligação à base de dados
$host = 'localhost';
$dbname = 'ficha_colaboradores';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}

// Consulta para obter os dados dos colaboradores
$stmt = $pdo->prepare("SELECT nome, genero, id_equipa, id_departamento, localidade, data_entrada FROM colaborador WHERE estado = 'Ativo'");
$stmt->execute();
$teamData = [
    'A' => ['generoData' => [0, 0, 0], 'funcaoData' => [0, 0, 0], 'geografiaData' => [0, 0, 0], 'tempoGeneroData' => [0, 0, 0]],
    'B' => ['generoData' => [0, 0, 0], 'funcaoData' => [0, 0, 0], 'geografiaData' => [0, 0, 0], 'tempoGeneroData' => [0, 0, 0]]
];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $team = ($row['id_equipa'] == 1 || $row['id_departamento'] == 1) ? 'A' : 'B';
    if ($row['genero'] == 'Masculino') $teamData[$team]['generoData'][0]++;
    elseif ($row['genero'] == 'Feminino') $teamData[$team]['generoData'][1]++;
    elseif ($row['genero'] == 'Outro' || $row['genero'] == 'Prefiro não dizer') $teamData[$team]['generoData'][2]++;
    if ($row['localidade'] == 'Lisboa') $teamData[$team]['geografiaData'][0]++;
    elseif ($row['localidade'] == 'Porto') $teamData[$team]['geografiaData'][1]++;
    elseif ($row['localidade'] == 'Coimbra') $teamData[$team]['geografiaData'][2]++;
    if ($row['data_entrada']) {
        $tenure = floor((time() - strtotime($row['data_entrada'])) / (365 * 24 * 60 * 60));
        if ($row['genero'] == 'Masculino') $teamData[$team]['tempoGeneroData'][0] += $tenure;
        elseif ($row['genero'] == 'Feminino') $teamData[$team]['tempoGeneroData'][1] += $tenure;
        elseif ($row['genero'] == 'Outro' || $row['genero'] == 'Prefiro não dizer') $teamData[$team]['tempoGeneroData'][2] += $tenure;
    }
}

// Normalizar tempo médio (dividir pelo número de colaboradores por género)
foreach (['A', 'B'] as $team) {
    $totalM = $teamData[$team]['generoData'][0] ? $teamData[$team]['tempoGeneroData'][0] / $teamData[$team]['generoData'][0] : 0;
    $totalF = $teamData[$team]['generoData'][1] ? $teamData[$team]['tempoGeneroData'][1] / $teamData[$team]['generoData'][1] : 0;
    $totalO = $teamData[$team]['generoData'][2] ? $teamData[$team]['tempoGeneroData'][2] / $teamData[$team]['generoData'][2] : 0;
    $teamData[$team]['tempoGeneroData'] = [$totalM, $totalF, $totalO];
}

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
      justify-content: center;
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
        <div class="metric-value" id="retentionRate">N/A</div>
        <div class="metric-legend">Percentagem de colaboradores retidos na empresa</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Idade Média</h2>
        <div class="metric-value" id="averageAge">N/A</div>
        <div class="metric-legend">Média de idade dos colaboradores da Tlantic</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Tempo Médio na Tlantic</h2>
        <div class="metric-value" id="averageTenure">N/A</div>
        <div class="metric-legend">Tempo médio de permanência dos colaboradores na empresa</div>
      </div>
      <div class="chart-box compact-box">
        <h2>Remuneração Média</h2>
        <div class="metric-value" id="averageSalary">N/A</div>
        <div class="metric-legend">Remuneração média mensal dos colaboradores</div>
      </div>
      <div class="charts-row">
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

    let chartGenero, chartFuncao, chartGeografia, chartTempoGenero;

    function switchTeam(team) {
      currentTeam = team;
      updateDashboard();
      document.getElementById('headerTeam').textContent = `Dashboard de Recursos Humanos - Equipa ${currentTeam}`;
      document.getElementById('btnEquipaA').className = currentTeam === 'A' ? 'active' : '';
      document.getElementById('btnEquipaB').className = currentTeam === 'B' ? 'active' : '';
    }

    function updateDashboard() {
      const currentData = teamData[currentTeam];

      // Update metrics (placeholder, adjust with DB queries if needed)
      document.getElementById('retentionRate').textContent = 'N/A';
      document.getElementById('averageAge').textContent = 'N/A';
      document.getElementById('averageTenure').textContent = 'N/A';
      document.getElementById('averageSalary').textContent = 'N/A';

      // Destroy existing charts if they exist
      if (chartGenero) chartGenero.destroy();
      if (chartFuncao) chartFuncao.destroy();
      if (chartGeografia) chartGeografia.destroy();
      if (chartTempoGenero) chartTempoGenero.destroy();

      // Chart: Distribuição por Género
      chartGenero = new Chart(document.getElementById('chartGenero'), {
        type: 'pie',
        data: {
          labels: ['Masculino', 'Feminino', 'Outro'],
          datasets: [{
            data: currentData.generoData,
            backgroundColor: ['#0077cc', '#80bfff', '#99ccff']
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom', labels: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' }, color: '#003366', boxWidth: 20, padding: 10 } }
          }
        }
      });

      // Chart: Distribuição por Função (placeholder, requires funcaoData from DB)
      chartFuncao = new Chart(document.getElementById('chartFuncao'), {
        type: 'bar',
        data: {
          labels: ['Desenvolvedor', 'Analista', 'Gestor'],
          datasets: [{
            data: [0, 0, 0], // Substituir por dados reais de funcaoData
            backgroundColor: ['#003f6b', '#005fa3', '#0077cc']
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
            backgroundColor: ['#004c99', '#0066cc', '#3399ff']
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
          labels: ['Masculino', 'Feminino', 'Outro'],
          datasets: [{
            data: currentData.tempoGeneroData,
            backgroundColor: ['#004c99', '#66b2ff', '#99ccff'],
            barThickness: 50
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, title: { display: true, text: 'Anos' } } }
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