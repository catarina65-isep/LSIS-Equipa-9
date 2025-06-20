<?php
$conn = new mysqli("localhost", "root", "", "ficha_colaboradores");
if ($conn->connect_error) {
  die("Erro de ligação: " . $conn->connect_error);
}
?>