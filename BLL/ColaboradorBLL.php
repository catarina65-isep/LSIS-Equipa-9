<?php
require_once __DIR__ . '/../DAL/database.php';

class ColaboradorBLL {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function obterColaboradoresAtivos() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT * FROM colaborador WHERE estado = 'Ativo'");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter colaboradores ativos: " . $e->getMessage());
            return [];
        }
    }

    public function obterTotalColaboradores() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT COUNT(*) as total FROM colaborador WHERE estado = 'Ativo'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro ao obter total de colaboradores: " . $e->getMessage());
            return 0;
        }
    }

    public function obterDistribuicaoGenero() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT genero, COUNT(*) as total FROM colaborador WHERE estado = 'Ativo' GROUP BY genero");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter distribuição por gênero: " . $e->getMessage());
            return [];
        }
    }

    public function obterDistribuicaoPais() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT pais, COUNT(*) as total FROM colaborador WHERE estado = 'Ativo' GROUP BY pais");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter distribuição por país: " . $e->getMessage());
            return [];
        }
    }

    public function obterDistribuicaoIdade() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT data_nascimento FROM colaborador WHERE estado = 'Ativo'");
            $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $idades = [];
            foreach ($colaboradores as $colab) {
                $dataNasc = new DateTime($colab['data_nascimento']);
                $hoje = new DateTime();
                $idade = $hoje->diff($dataNasc)->y;
                $idades[] = $idade;
            }
            
            return $idades;
        } catch (PDOException $e) {
            error_log("Erro ao obter distribuição por idade: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Erro ao calcular idades: " . $e->getMessage());
            return [];
        }
    }
}
?>
