<?php
// Conexão à base de dados
$host = 'localhost';
$db = 'ficha_colaboradores';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
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
            padding: 20px 40px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
    <h1>Painel do Administrador</h1>
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
        $sql_alertas = "SELECT titulo, descricao, tipo, categoria, prioridade, status, data_criacao 
                        FROM alerta ORDER BY data_criacao DESC";
        $result_alertas = $conn->query($sql_alertas);
        if ($result_alertas->num_rows > 0) {
            while ($row = $result_alertas->fetch_assoc()) {
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
        $sql_logins = "SELECT id_utilizador, acao, modulo, ip, data_acesso 
                       FROM historico_acesso 
                       WHERE acao LIKE '%login%' 
                       ORDER BY data_acesso DESC LIMIT 50";
        $result_logins = $conn->query($sql_logins);
        if ($result_logins->num_rows > 0) {
            while ($row = $result_logins->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_utilizador']}</td>
                        <td>{$row['acao']}</td>
                        <td>{$row['modulo']}</td>
                        <td>{$row['ip']}</td>
                        <td>{$row['data_acesso']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='no-data'>Nenhum login registado.</td></tr>";
        }
        ?>
    </table>
</main>

<?php $conn->close(); ?>

</body>
</html>
