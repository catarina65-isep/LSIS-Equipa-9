<?php
// Configurações de conexão com o banco de dados
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lsis_equipa9';

// Criar conexão
$conn = new mysqli($host, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Setar charset para UTF-8
$conn->set_charset("utf8mb4");
?>
