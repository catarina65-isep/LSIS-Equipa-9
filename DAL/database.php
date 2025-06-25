<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ficha_colaboradores';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec('set names utf8');
        } catch(PDOException $e) {
            error_log('Erro de conexão: ' . $e->getMessage());
            throw new Exception('Erro ao conectar ao banco de dados.');
        }

        return $this->conn;
    }
}
?>
