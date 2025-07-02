<?php
// Função de autoload para carregar classes automaticamente
spl_autoload_register(function ($class) {
    // Mapeamento de diretórios onde as classes podem estar
    $directories = [
        __DIR__ . '/BLL/',
        __DIR__ . '/DAL/'
    ];
    
    // Tenta encontrar a classe em cada diretório
    foreach ($directories as $directory) {
        // Try exact match first
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        
        // Try lowercase filename
        $file = $directory . strtolower($class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        
        // Try first letter lowercase
        $file = $directory . lcfirst($class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Inclui o arquivo de configuração do banco de dados
require_once __DIR__ . '/DAL/config.php';

// A sessão é iniciada no header.php

// Define constantes úteis
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/LSIS-Equipa-9');
?>
