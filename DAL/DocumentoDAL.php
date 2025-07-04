<?php
require_once __DIR__ . '/config.php';

class DocumentoDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Conta o número total de documentos
     * 
     * @return int Número total de documentos
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM documento";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Conta documentos por status
     * 
     * @param string $status Status do documento (Pendente, Aprovado, Rejeitado, etc.)
     * @return int Número de documentos com o status especificado
     */
    public function contarPorStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM documento WHERE status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Conta documentos expirados
     * 
     * @return int Número de documentos expirados
     */
    public function contarExpirados() {
        $sql = "SELECT COUNT(*) as total FROM documento 
                WHERE data_vencimento < CURDATE() 
                AND status != 'Expirado'";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Conta documentos que irão vencer nos próximos dias
     * 
     * @param int $dias Número de dias para considerar como "próximo"
     * @return int Número de documentos que irão vencer
     */
    public function contarProximosVencer($dias = 7) {
        $sql = "SELECT COUNT(*) as total FROM documento 
                WHERE data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                AND status = 'Ativo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dias' => $dias]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Lista os próximos documentos a vencer
     * 
     * @param int $limite Número máximo de documentos a retornar
     * @return array Lista de documentos
     */
    public function listarProximosVencimentos($limite = 5) {
        try {
            // Primeiro, verifica se a tabela documento existe
            $checkTable = $this->pdo->query("SHOW TABLES LIKE 'documento'");
            
            if ($checkTable->rowCount() === 0) {
                // Se a tabela não existir, retorna um array vazio
                return [];
            }
            
            // Verifica se a coluna data_vencimento existe
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM documento LIKE 'data_vencimento'");
            
            if ($checkColumn->rowCount() === 0) {
                // Se a coluna não existir, faz uma consulta sem ela
                $sql = "SELECT d.*, c.nome as colaborador_nome, t.nome as tipo_documento
                        FROM documento d
                        JOIN colaborador c ON d.id_colaborador = c.id_colaborador
                        JOIN tipo_documento t ON d.id_tipo_documento = t.id_tipo_documento
                        WHERE d.status = 'Ativo'
                        ORDER BY d.data_atualizacao DESC
                        LIMIT :limite";
            } else {
                // Se a coluna existir, usa a consulta original
                $sql = "SELECT d.*, c.nome as colaborador_nome, t.nome as tipo_documento
                        FROM documento d
                        JOIN colaborador c ON d.id_colaborador = c.id_colaborador
                        JOIN tipo_documento t ON d.id_tipo_documento = t.id_tipo_documento
                        WHERE (d.data_vencimento IS NULL OR d.data_vencimento >= CURDATE())
                        AND d.status = 'Ativo'
                        ORDER BY d.data_vencimento ASC, d.data_atualizacao DESC
                        LIMIT :limite";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Em caso de erro, retorna um array vazio para evitar quebrar a aplicação
            error_log("Erro ao listar próximos vencimentos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém a contagem de documentos por tipo
     * 
     * @return array Contagem de documentos por tipo
     */
    public function obterContagemPorTipo() {
        $sql = "SELECT 
                    t.nome as tipo_documento,
                    COUNT(d.id_documento) as total,
                    COUNT(CASE WHEN d.data_vencimento < CURDATE() THEN 1 END) as expirados,
                    COUNT(CASE WHEN d.status = 'Pendente' THEN 1 END) as pendentes
                FROM tipo_documento t
                LEFT JOIN documento d ON t.id_tipo_documento = d.id_tipo_documento
                GROUP BY t.id_tipo_documento, t.nome
                ORDER BY total DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém a contagem de documentos por status
     * 
     * @return array Contagem de documentos por status
     */
    public function obterContagemPorStatus() {
        $sql = "SELECT 
                    status,
                    COUNT(*) as total,
                    ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM documento), 2) as percentual
                FROM documento
                GROUP BY status
                ORDER BY total DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
