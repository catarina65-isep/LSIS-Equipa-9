<?php
require_once __DIR__ . '/config.php';

class MetricasRH {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getTaxaRetencao() {
        $sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE 
                WHEN estado = 'Ativo' THEN 1 
                WHEN estado = 'Inativo' AND data_saida IS NULL THEN 1 
                ELSE 0 
            END) as ativos
            FROM colaborador";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            return round(($row['ativos'] / $row['total']) * 100, 1) . '%';
        }
        return '0.0%';
    }

    public function getIdadeMedia() {
        $sql = "SELECT 
            AVG(TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE())) as idade_media
            FROM colaborador
            WHERE estado = 'Ativo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return round($row['idade_media'], 1) . ' anos';
    }

    public function getTempoMedioPermanencia() {
        $sql = "SELECT 
            AVG(TIMESTAMPDIFF(YEAR, data_entrada, CURDATE())) as tempo_medio
            FROM colaborador
            WHERE estado = 'Ativo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return round($row['tempo_medio'], 1) . ' anos';
    }

    public function getSalarioMedio() {
        $sql = "SELECT 
            AVG(remuneracao_bruta) as salario_medio
            FROM colaborador
            WHERE estado = 'Ativo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return number_format($row['salario_medio'], 2, ',', '.') . ' €';
    }

    public function getDistribuicaoHierarquia() {
        $sql = "SELECT 
            f.titulo as funcao,
            COUNT(*) as total
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo'
            GROUP BY f.titulo
            ORDER BY total DESC
            LIMIT 3";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (int)$row['total'];
        }
        
        return $dados;
    }

    public function getDistribuicaoGenero() {
        $sql = "SELECT 
            COUNT(*) as total,
            genero
            FROM colaborador
            WHERE estado = 'Ativo'
            GROUP BY genero";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (int)$row['total'];
        }
        
        return $dados;
    }

    public function getDistribuicaoFuncao() {
        $sql = "SELECT 
            COUNT(*) as total,
            f.titulo as funcao
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo'
            GROUP BY f.titulo
            ORDER BY total DESC
            LIMIT 3";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (int)$row['total'];
        }
        
        return $dados;
    }

    public function getDistribuicaoGeografia() {
        $sql = "SELECT 
            COUNT(*) as total,
            departamento.nome as departamento
            FROM colaborador
            JOIN departamento ON colaborador.id_departamento = departamento.id_departamento
            WHERE colaborador.estado = 'Ativo'
            GROUP BY departamento.nome
            ORDER BY total DESC
            LIMIT 3";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (int)$row['total'];
        }
        
        return $dados;
    }

    public function getTempoPorGenero() {
        $sql = "SELECT 
            genero,
            AVG(TIMESTAMPDIFF(YEAR, data_entrada, CURDATE())) as tempo_medio
            FROM colaborador
            WHERE estado = 'Ativo'
            GROUP BY genero";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (float)$row['tempo_medio'];
        }
        
        return $dados;
    }

    public function getRemuneracaoPorFuncao() {
        $sql = "SELECT 
            f.titulo as funcao,
            AVG(remuneracao_bruta) as salario_medio
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo'
            GROUP BY f.titulo
            ORDER BY salario_medio DESC
            LIMIT 3";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (float)$row['salario_medio'];
        }
        
        return $dados;
    }

    public function getHierarquiaPorIdade() {
        $sql = "SELECT 
            f.titulo,
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, c.data_nascimento, CURRENT_DATE) < 30 THEN 'Menos de 30 anos'
                WHEN TIMESTAMPDIFF(YEAR, c.data_nascimento, CURRENT_DATE) BETWEEN 30 AND 40 THEN '30-40 anos'
                WHEN TIMESTAMPDIFF(YEAR, c.data_nascimento, CURRENT_DATE) BETWEEN 41 AND 50 THEN '41-50 anos'
                WHEN TIMESTAMPDIFF(YEAR, c.data_nascimento, CURRENT_DATE) BETWEEN 51 AND 60 THEN '51-60 anos'
                ELSE 'Mais de 60 anos'
            END as faixa_etaria,
            COUNT(*) as total
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo'
            GROUP BY f.titulo, faixa_etaria
            ORDER BY f.titulo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $dados = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = (int)$row['total'];
        }
        
        return $dados;
    }
}
