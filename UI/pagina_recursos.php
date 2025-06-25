<?php
// conexao.php
$host = 'localhost';
$db = 'ficha_colaboradores';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<!-- recursos_humanos.php -->
<?php
require_once 'conexao.php';

$query = "SELECT c.id_colaborador, c.nome, c.apelido, c.email, c.telefone, c.estado, c.tipo_contrato, f.titulo AS funcao, e.nome AS equipa
          FROM colaborador c
          LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
          LEFT JOIN equipa e ON c.id_equipa = e.id_equipa
          ORDER BY c.nome";

$stmt = $pdo->prepare($query);
$stmt->execute();
$colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Recursos Humanos</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h2>Lista de Colaboradores</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Função</th>
                <th>Equipa</th>
                <th>Contrato</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($colaboradores as $colaborador): ?>
            <tr>
                <td><?= $colaborador['id_colaborador'] ?></td>
                <td><?= $colaborador['nome'] . ' ' . $colaborador['apelido'] ?></td>
                <td><?= $colaborador['email'] ?></td>
                <td><?= $colaborador['telefone'] ?></td>
                <td><?= $colaborador['funcao'] ?></td>
                <td><?= $colaborador['equipa'] ?></td>
                <td><?= $colaborador['tipo_contrato'] ?></td>
                <td><?= $colaborador['estado'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>