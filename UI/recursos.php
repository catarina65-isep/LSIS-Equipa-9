<?php
$host = 'localhost';
$db   = 'ficha_colaboradores';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

// Conexão à base de dados
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query("SELECT id_colaborador, nome, email, estado FROM colaborador");
    $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    echo "Erro na ligação à base de dados: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Painel RH</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
  <h1 class="text-3xl font-bold mb-6 text-gray-800">Painel de Recursos Humanos</h1>

  <div class="bg-white shadow-md rounded p-4">
    <h2 class="text-xl font-semibold mb-4">Lista de Colaboradores</h2>
    <table class="min-w-full table-auto">
      <thead>
        <tr class="bg-gray-200">
          <th class="px-4 py-2 text-left">ID</th>
          <th class="px-4 py-2 text-left">Nome</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($colaboradores as $colaborador): ?>
        <tr class="border-b">
          <td class="px-4 py-2"><?= htmlspecialchars($colaborador['id_colaborador']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($colaborador['nome']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($colaborador['email']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($colaborador['estado']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
