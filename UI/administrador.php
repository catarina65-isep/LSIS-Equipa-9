<?php
// admin_alertas_logins.php
$host = 'localhost';
$db = 'ficha_colaboradores';
$user = 'root';
$pass = 'root';
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
        body { font-family: Arial; background-color: #f4f4f4; margin: 0; padding: 20px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #3498db; color: white; }
    </style>
</head>
<body>
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
            while($row = $result_alertas->fetch_assoc()) {
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
            echo "<tr><td colspan='7'>Nenhum alerta encontrado.</td></tr>";
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
            while($row = $result_logins->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_utilizador']}</td>
                        <td>{$row['acao']}</td>
                        <td>{$row['modulo']}</td>
                        <td>{$row['ip']}</td>
                        <td>{$row['data_acesso']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nenhum login registado.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
