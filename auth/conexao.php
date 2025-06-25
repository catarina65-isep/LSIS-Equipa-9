<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db = "ficha_colaboradores";


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
