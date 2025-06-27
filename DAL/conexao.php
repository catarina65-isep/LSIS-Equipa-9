<?php
// Arquivo de compatibilidade para manter compatibilidade com código antigo
// Este arquivo simplesmente inclui o arquivo config.php que contém a classe Database

require_once __DIR__ . '/config.php';

// Cria um alias para a classe Database como Conexao
class_alias('Database', 'Conexao');
?>
