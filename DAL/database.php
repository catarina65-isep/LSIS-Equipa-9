<?php
// This file is deprecated. Please use config.php instead.
require_once __DIR__ . '/config.php';

// For backward compatibility only
class_alias('Database', 'DatabaseDeprecated');
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
