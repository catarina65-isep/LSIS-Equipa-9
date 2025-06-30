<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ficha_colaboradores';
    private $username = 'root';
    private $password = 'root'; // Senha padrão do MAMP
    private $port = 8889; // Porta padrão do MAMP
    private $socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};unix_socket={$this->socket}";
            
            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $e) {
            error_log('Erro de conexão: ' . $e->getMessage());
            throw new Exception('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }

        return $this->conn;
    }
}
?>
