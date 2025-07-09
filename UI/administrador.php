<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Inclui os arquivos necessários
require_once __DIR__ . '/../BLL/UtilizadorBLL.php';
require_once __DIR__ . '/../BLL/AlertaBLL.php';
require_once __DIR__ . '/../DAL/config.php';

$utilizadorBLL = new UtilizadorBLL();
$alertaBLL = new AlertaBLL();

// Obtém a conexão PDO
$db = Database::getInstance();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador - Alertas e Logins</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #eef4fb;
        }

        header {
            background-color: #005baf;
            color: white;
            padding: 30px 40px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .logout-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #dc3545;
            color: white !important;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logout-button:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .logout-button:active {
            transform: translateY(0);
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        main {
            padding: 40px;
        }

        h2 {
            color: #004080;
            margin-top: 40px;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f4f9ff;
        }

        tr:hover {
            background-color: #e2efff;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
        }
    </style>
</head>
<body>

<header>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0;">
        <h1>Painel do Administrador</h1>
        <a href="/LSIS-Equipa-9/UI/processa_logout.php" class="logout-button">
            Sair
        </a>
    </div>
</header>

<main>
    <h2>📢 Alertas Automáticos</h2>
    <table>
        <tr>
            <th>Título</th>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Prioridade</th>
            <th>Status</th>
            <th>Data Criação</th>
        </tr>
        <?php
        try {
            $stmt = $db->prepare("SELECT titulo, descricao, tipo, categoria, prioridade, status, data_criacao 
                                 FROM alerta ORDER BY data_criacao DESC");
            $stmt->execute();
            $result_alertas = $stmt->fetchAll();
            
            if (!empty($result_alertas)) {
                foreach ($result_alertas as $row) {
                    echo "<tr>
                            <td>{$row['titulo']}</td>
                            <td>{$row['descricao']}</td>
                            <td>{$row['tipo']}</td>
                            <td>{$row['categoria']}</td>
                            <td>{$row['prioridade']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['data_criacao']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='no-data'>Nenhum alerta encontrado.</td></tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='7' class='error'>Erro ao carregar alertas: " . $e->getMessage() . "</td></tr>";
        }
        ?>
    </table>

    <h2>🔐 Logins Recentes</h2>
    <table>
        <tr>
            <th>Utilizador ID</th>
            <th>Ação</th>
            <th>Módulo</th>
            <th>IP</th>
            <th>Data de Acesso</th>
        </tr>
        <?php
        try {
            $stmt = $db->prepare("SELECT id_utilizador, acao, modulo, ip, data_acesso 
                                 FROM historico_acesso 
                                 WHERE acao LIKE '%login%' 
                                 ORDER BY data_acesso DESC LIMIT 50");
            $stmt->execute();
            $result_logins = $stmt->fetchAll();
            
            if (!empty($result_logins)) {
                foreach ($result_logins as $row) {
                    echo "<tr>
                            <td>{$row['id_utilizador']}</td>
                            <td>{$row['acao']}</td>
                            <td>{$row['modulo']}</td>
                            <td>{$row['ip']}</td>
                            <td>{$row['data_acesso']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='no-data'>Nenhum login recente encontrado.</td></tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='5' class='error'>Erro ao carregar logins: " . $e->getMessage() . "</td></tr>";
        }
        ?>
    </table>
</main>

<?php $db = null; ?>

</body>
</html>
