<?php
// Inclui os arquivos necessários
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Usar a conexão existente do header.php
$pdo = Database::getInstance();

// Consulta para obter os dados das equipas mapeadas
$stmt = $pdo->prepare("
    SELECT c.nome, c.genero, c.localidade, c.data_entrada, e.nome AS equipa_nome, c.data_nascimento, c.remuneracao_bruta
    FROM colaborador c
    LEFT JOIN equipa e ON c.id_equipa = e.id_equipa
    WHERE e.nome IN ('equipa1', 'equipa2') AND c.estado = 'Ativo'
");
$stmt->execute();

// Debug - Verificar se temos dados
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($results)) {
    die('Nenhum colaborador encontrado nas equipas especificadas');
}

// Debug - Verificar valores dos campos
echo "<pre>";
foreach ($results as $row) {
    echo "Colaborador: " . $row['nome'] . "\n";
    echo "Data Nascimento: " . ($row['data_nascimento'] ?? 'NULL') . "\n";
    echo "Data Entrada: " . ($row['data_entrada'] ?? 'NULL') . "\n";
    echo "Remuneração Bruta: " . ($row['remuneracao_bruta'] ?? 'NULL') . "\n";
    echo "\n";
}
echo "</pre>";

// Inicializar teamData com valores iniciais como 0
$teamData = [
    'A' => [
        'generoData' => [0, 0, 0], 
        'geografiaData' => [0, 0, 0], 
        'tempoGeneroData' => [0, 0, 0],
        'idade_media' => 0,
        'tempo_medio' => 0,
        'salario_medio' => 0,
        'total_colaboradores' => 0
    ],
    'B' => [
        'generoData' => [0, 0, 0], 
        'geografiaData' => [0, 0, 0], 
        'tempoGeneroData' => [0, 0, 0],
        'idade_media' => 0,
        'tempo_medio' => 0,
        'salario_medio' => 0,
        'total_colaboradores' => 0
    ]
];

// Processar dados
foreach ($results as $row) {
    $team = ($row['equipa_nome'] === 'equipa1') ? 'A' : 'B'; // Mapeia equipa1 para A, equipa2 para B
    $genero = $row['genero'] ?? 'Outro';
    if ($genero == 'Masculino') $teamData[$team]['generoData'][0]++;
    elseif ($genero == 'Feminino') $teamData[$team]['generoData'][1]++;
    else $teamData[$team]['generoData'][2]++;
    $localidade = $row['localidade'] ?? 'Outros';
    if ($localidade == 'Lisboa') $teamData[$team]['geografiaData'][0]++;
    elseif ($localidade == 'Porto') $teamData[$team]['geografiaData'][1]++;
    elseif ($localidade == 'Coimbra') $teamData[$team]['geografiaData'][2]++;
    
    // Incrementar total de colaboradores
    $teamData[$team]['total_colaboradores']++;
    
    // Calcular médias
    if ($row['data_nascimento']) {
        $dataNascimento = new DateTime($row['data_nascimento']);
        $dataAtual = new DateTime();
        $idade = $dataAtual->diff($dataNascimento)->y;
        $teamData[$team]['idade_media'] += $idade;
    }
    
    if ($row['data_entrada']) {
        $dataEntrada = new DateTime($row['data_entrada']);
        $dataAtual = new DateTime();
        $tempo = $dataAtual->diff($dataEntrada)->y + ($dataAtual->diff($dataEntrada)->m / 12);
        $teamData[$team]['tempo_medio'] += $tempo;
        
        // Adicionar tempo ao tempoGeneroData
        if ($genero == 'Masculino') $teamData[$team]['tempoGeneroData'][0] += $tempo;
        elseif ($genero == 'Feminino') $teamData[$team]['tempoGeneroData'][1] += $tempo;
        else $teamData[$team]['tempoGeneroData'][2] += $tempo;
    }
    
    if ($row['remuneracao_bruta']) {
        $teamData[$team]['salario_medio'] += floatval($row['remuneracao_bruta']);
    }
}

// Calcular médias finais
foreach (['A', 'B'] as $team) {
    // Usar o total de colaboradores contado
    $totalColaboradores = $teamData[$team]['total_colaboradores'];
    
    // Calcular médias apenas se houver colaboradores
    if ($totalColaboradores > 0) {
        // Calcular médias aritméticas simples
        $teamData[$team]['idade_media'] = $teamData[$team]['idade_media'] / $totalColaboradores;
        $teamData[$team]['tempo_medio'] = $teamData[$team]['tempo_medio'] / $totalColaboradores;
        $teamData[$team]['salario_medio'] = $teamData[$team]['salario_medio'] / $totalColaboradores;
        
        // Arredondar valores
        $teamData[$team]['idade_media'] = round($teamData[$team]['idade_media'], 1);
        $teamData[$team]['tempo_medio'] = round($teamData[$team]['tempo_medio'], 1);
        $teamData[$team]['salario_medio'] = round($teamData[$team]['salario_medio'], 2);
    } else {
        // Se não houver colaboradores, definir valores como null
        $teamData[$team]['idade_media'] = null;
        $teamData[$team]['tempo_medio'] = null;
        $teamData[$team]['salario_medio'] = null;
    }
 
    // Calcular médias de tempo por gênero
    $totalM = $teamData[$team]['generoData'][0] ? $teamData[$team]['tempoGeneroData'][0] / $teamData[$team]['generoData'][0] : 0;
    $totalF = $teamData[$team]['generoData'][1] ? $teamData[$team]['tempoGeneroData'][1] / $teamData[$team]['generoData'][1] : 0;
    $totalO = $teamData[$team]['generoData'][2] ? $teamData[$team]['tempoGeneroData'][2] / $teamData[$team]['generoData'][2] : 0;
    $teamData[$team]['tempoGeneroData'] = [$totalM, $totalF, $totalO];
    
    // Debug - Verificar métricas calculadas
    echo "<pre>Métricas para equipe " . ($team === 'A' ? 'equipa1' : 'equipa2') . ":\n";
    echo "Idade média: " . ($teamData[$team]['idade_media'] ?? 'NULL') . "\n";
    echo "Tempo médio: " . ($teamData[$team]['tempo_medio'] ?? 'NULL') . "\n";
    echo "Salário médio: " . ($teamData[$team]['salario_medio'] ?? 'NULL') . "\n";
    echo "</pre>";
}

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
  <script>
    // Debug - Verificar dados do PHP
    console.log('Dados do PHP:', <?php echo json_encode($teamData); ?>);
  </script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f9ff;
      color: #003366;
    }
    
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 10px;
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

  </header>

  <div class="main-content">
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
    // Garantir que o DOM esteja pronto antes de executar qualquer código
    document.addEventListener('DOMContentLoaded', function() {
      // Configurações do Chart.js
      Chart.defaults.font.family = "'Segoe UI', sans-serif";
      Chart.defaults.font.size = 14;
      Chart.defaults.font.weight = '500';
      Chart.defaults.color = '#003366';

      // Garantir que os dados do PHP estão disponíveis
      const teamData = <?php echo json_encode($teamData); ?>;
      let currentTeam = '<?php echo $currentTeam; ?>'; // Declarar como let para ser modificável

      // Função para atualizar o dashboard
      function updateDashboard() {
        const currentData = teamData[currentTeam];
        
        // Debug - Verificar dados atuais
        console.log('Atualizando dashboard para equipe:', currentTeam);
        console.log('Dados da equipe:', currentData);

        // Verificar se os elementos existem antes de atualizar
        const elements = {
          retentionRate: document.getElementById('retentionRate'),
          averageAge: document.getElementById('averageAge'),
          averageTenure: document.getElementById('averageTenure'),
          averageSalary: document.getElementById('averageSalary')
        };

        // Debug - Verificar se os elementos existem
        console.log('Elementos encontrados:', elements);

        // Verificar se todos os elementos necessários existem e se os dados estão disponíveis
        const allElementsExist = Object.values(elements).every(element => element !== null);
        const allDataExists = currentData.idade_media !== null && currentData.tempo_medio !== null && currentData.salario_medio !== null;
        
        if (!allElementsExist || !allDataExists) {
          console.error('Elementos HTML não encontrados ou dados não disponíveis');
          return;
        }

        // Atualizar métricas
        if (elements.retentionRate) {
          const totalColaboradores = currentData.generoData.reduce((a, b) => a + b, 0);
          elements.retentionRate.textContent = totalColaboradores > 0 ? ((totalColaboradores / totalColaboradores) * 100).toFixed(1) + '%' : 'N/A';
        }

        if (elements.averageAge) {
          elements.averageAge.textContent = currentData.idade_media ? currentData.idade_media.toFixed(1) + ' anos' : 'N/A';
        }

        if (elements.averageTenure) {
          elements.averageTenure.textContent = currentData.tempo_medio ? currentData.tempo_medio.toFixed(1) + ' anos' : 'N/A';
        }

        if (elements.averageSalary) {
          elements.averageSalary.textContent = currentData.salario_medio ? currentData.salario_medio.toFixed(2) + ' €' : 'N/A';
        }

        // Debug - Verificar valores atualizados
        console.log('Taxa de retenção:', elements.retentionRate.textContent);
        console.log('Idade média:', elements.averageAge.textContent);
        console.log('Tempo médio:', elements.averageTenure.textContent);
        console.log('Salário médio:', elements.averageSalary.textContent);
      }

      // Inicializar gráficos
      try {
        // Gráfico de Género - Barras Horizontais
        const chartGenero = new Chart(document.getElementById('chartGenero'), {
          type: 'bar',
          data: {
            labels: ['Masculino', 'Feminino', 'Outro'],
            datasets: [{
              data: teamData[currentTeam].generoData,
              backgroundColor: ['#004c99', '#0066cc', '#3399ff'],
              borderColor: '#003366',
              borderWidth: 1,
              borderRadius: 5,
              barPercentage: 0.9,
              categoryPercentage: 0.9
            }]
          },
          options: {
            indexAxis: 'y',
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    let label = context.label || '';
                    if (label) {
                      label += ': ';
                    }
                    if (context.parsed) {
                      label += context.parsed + ' colaboradores';
                    }
                    return label;
                  }
                }
              }
            },
            scales: {
              x: {
                beginAtZero: true,
                grid: {
                  color: '#003366',
                  lineWidth: 1
                },
                ticks: {
                  font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' },
                  color: '#003366'
                }
              },
              y: {
                grid: {
                  display: false
                },
                ticks: {
                  font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' },
                  color: '#003366'
                }
              }
            },
            layout: {
              padding: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 20
              }
            }
          }
        });

        // Gráfico de Geografia
        const chartGeografia = new Chart(document.getElementById('chartGeografia'), {
          type: 'pie',
          data: {
            labels: ['Lisboa', 'Porto', 'Coimbra'],
            datasets: [{
              data: teamData[currentTeam].geografiaData,
              backgroundColor: ['#004c99', '#0066cc', '#3399ff']
            }]
          },
          options: {
            plugins: {
              legend: { position: 'bottom', labels: { font: { size: 14, family: "'Segoe UI', sans-serif", weight: '500' }, color: '#003366', boxWidth: 20, padding: 10 } }
            }
          }
        });

        // Gráfico de Tempo por Gênero
        const chartTempoGenero = new Chart(document.getElementById('chartTempoGenero'), {
          type: 'bar',
          data: {
            labels: ['Masculino', 'Feminino', 'Outro'],
            datasets: [{
              data: teamData[currentTeam].tempoGeneroData,
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

        // Atualizar gráficos quando a equipe mudar
        function updateCharts() {
          // Atualizar dados dos gráficos
          chartGeografia.data.datasets[0].data = teamData[currentTeam].geografiaData;
          chartTempoGenero.data.datasets[0].data = teamData[currentTeam].tempoGeneroData;
          chartGenero.data.datasets[0].data = teamData[currentTeam].generoData;
          
          // Atualizar gráficos
          chartGeografia.update();
          chartTempoGenero.update();
          chartGenero.update();
        }

        // Função para trocar de equipe
        function switchTeam(team) {
          console.log('Tentando trocar para equipe:', team);
          currentTeam = team;
          console.log('Equipe atualizada para:', currentTeam);
          updateDashboard();
          updateCharts();
          document.getElementById('btnEquipaA').classList.toggle('active', team === 'A');
          document.getElementById('btnEquipaB').classList.toggle('active', team === 'B');
        }
      } catch (error) {
        console.error('Erro ao inicializar gráficos:', error);
      }

      // Adicionar função global para troca de equipe
      window.switchTeam = switchTeam;

      // Inicializar dashboard quando a página carregar
      window.onload = function() {
        console.log('Iniciando dashboard para equipe:', currentTeam);
        updateDashboard();
      };
    });
  </script>
</body>
</html>