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
        // Primeiro, contar os colaboradores por gênero
        $sql = "SELECT 
            COUNT(*) as total,
            genero
            FROM colaborador
            WHERE estado = 'Ativo'
            GROUP BY genero";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        // Inicializar os valores com 0
        $masculino = 0;
        $feminino = 0;
        $outros = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['genero'] === 'Masculino') {
                $masculino = (int)$row['total'];
            } elseif ($row['genero'] === 'Feminino') {
                $feminino = (int)$row['total'];
            } else { // NULL ou outro valor
                $outros = (int)$row['total'];
            }
        }
        
        return [
            'labels' => ['Masculino', 'Feminino', 'Outros'],
            'data' => [$masculino, $feminino, $outros]
        ];
    }

    public function getDistribuicaoFuncao() {
        // Primeiro, contar os colaboradores sem função
        $sqlNull = "SELECT COUNT(*) as total FROM colaborador WHERE estado = 'Ativo' AND id_funcao IS NULL";
        $stmtNull = $this->db->prepare($sqlNull);
        $stmtNull->execute();
        $nullCount = $stmtNull->fetch(PDO::FETCH_ASSOC)['total'];

        // Depois, contar os colaboradores com função
        $sql = "SELECT 
            COUNT(*) as total,
            f.titulo as funcao
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo' AND c.id_funcao IS NOT NULL
            GROUP BY f.titulo
            ORDER BY total DESC
            LIMIT 2"; // Limitado a 2 porque queremos adicionar as outras funções
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        // Separar os dados em totais e rótulos
        $totais = [];
        $rotulos = [];
        
        // Adicionar os dois primeiros cargos
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $totais[] = (int)$row['total'];
            $rotulos[] = $row['funcao'];
        }

        // Adicionar os colaboradores sem função e os restantes cargos como "Outras Funções"
        $sqlOutras = "SELECT SUM(total) as total FROM (
            SELECT COUNT(*) as total
            FROM colaborador c
            LEFT JOIN funcao f ON c.id_funcao = f.id_funcao
            WHERE c.estado = 'Ativo' AND c.id_funcao IS NOT NULL
            GROUP BY f.titulo
            ORDER BY total DESC
            LIMIT 3, 1000
        ) as subquery";
        
        $stmtOutras = $this->db->prepare($sqlOutras);
        $stmtOutras->execute();
        $outrosCount = $stmtOutras->fetch(PDO::FETCH_ASSOC)['total'];

        // Somar os colaboradores sem função com os outros cargos
        $totais[] = (int)$nullCount + (int)$outrosCount;
        $rotulos[] = 'Outras Funções';
        
        return [
            'labels' => $rotulos,
            'data' => $totais
        ];    
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
    // Primeiro, calcular o tempo médio por gênero
    $sql = "SELECT 
        genero,
        AVG(TIMESTAMPDIFF(YEAR, data_entrada, CURDATE())) as tempo_medio
        FROM colaborador
        WHERE estado = 'Ativo'
        GROUP BY genero";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    // Inicializar os valores com 0
    $masculino = 0;
    $feminino = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['genero'] === 'Masculino') {
            $masculino = (float)$row['tempo_medio'];
        } elseif ($row['genero'] === 'Feminino') {
            $feminino = (float)$row['tempo_medio'];
        }
    }
    
    return [
        'labels' => ['Masculino', 'Feminino'],
        'data' => [
            round($masculino, 1),
            round($feminino, 1)
        ]
    ];
}

    public function getRemuneracaoPorHierarquia() {
        $sql = "SELECT 
            f.titulo as funcao,
            AVG(c.remuneracao_bruta) as salario_medio
            FROM colaborador c
            JOIN funcao f ON c.id_funcao = f.id_funcao  -- Mudando para JOIN simples
            WHERE c.estado = 'Ativo'
            GROUP BY f.titulo
            ORDER BY f.titulo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $dados = [];
        $labels = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $funcao = trim($row['funcao']);
            if (!empty($funcao)) {
                $labels[] = $funcao;
                $dados[] = (float)$row['salario_medio'];
            }
        }
        
        // Se não houver dados, retornar array vazio
        if (empty($labels)) {
            return [
                'labels' => ['Nenhuma função encontrada'],
                'data' => [0]
            ];
        }
        
        // Ordenar os dados para garantir que estão na mesma ordem
        array_multisort($labels, SORT_ASC, $dados);
        
        return [
            'labels' => $labels,
            'data' => $dados
        ];
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
