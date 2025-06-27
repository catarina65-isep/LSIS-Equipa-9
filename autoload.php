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
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Inclui o arquivo de configuração do banco de dados
require_once __DIR__ . '/DAL/config.php';

// Inicializa a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constantes úteis
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/LSIS-Equipa-9');
?>
