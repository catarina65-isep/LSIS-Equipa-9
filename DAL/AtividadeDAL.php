<?php
require_once __DIR__ . '/config.php';

class AtividadeDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Registra uma nova atividade no sistema
     * 
     * @param int $usuarioId ID do usuário
     * @param string $email Email do usuário
     * @param string $tipoAtividade Tipo de atividade (ex: login, logout)
     * @param string $ipAddress Endereço IP do usuário
     * @param string|null $userAgent User Agent do navegador
     * @param string $status Status da atividade (sucesso, erro)
     * @param string|null $mensagem Mensagem adicional (opcional)
     * @return bool True em caso de sucesso, False em caso de erro
     */
    public function registrarAtividade($usuarioId, $email, $tipoAtividade, $ipAddress, $userAgent = null, $status = 'sucesso', $mensagem = null) {
        try {
            $sql = "INSERT INTO atividades (
                        id_utilizador, 
                        email, 
                        tipo_atividade, 
                        ip_address, 
                        user_agent, 
                        status, 
                        mensagem,
                        data_hora
                    ) VALUES (
                        :id_utilizador, 
                        :email, 
                        :tipo_atividade, 
                        :ip_address, 
                        :user_agent, 
                        :status, 
                        :mensagem,
                        NOW()
                    )";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id_utilizador' => $usuarioId,
                ':email' => $email,
                ':tipo_atividade' => $tipoAtividade,
                ':ip_address' => $ipAddress,
                ':user_agent' => $userAgent,
                ':status' => $status,
                ':mensagem' => $mensagem
            ]);
            
        } catch (PDOException $e) {
            error_log("Erro ao registrar atividade: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém as atividades recentes do sistema
     * 
     * @param int $limite Número máximo de registros a retornar
     * @return array Lista de atividades
     */
    public function obterAtividadesRecentes($limite = 10) {
        error_log("Tentando obter $limite atividades recentes");
        try {
            $sql = "SELECT 
                        a.*,
                        a.id_utilizador,
                        a.email,
                        a.tipo_atividade,
                        a.data_hora,
                        a.ip_address,
                        a.user_agent,
                        a.status,
                        a.mensagem,
                        a.email as nome_utilizador  -- Usando o email como nome temporário
                    FROM 
                        atividades a
                    ORDER BY 
                        a.data_hora DESC 
                    LIMIT :limite";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $erro = "Erro ao obter atividades recentes: " . $e->getMessage();
            error_log($erro);
            // Verificar se a tabela existe
            try {
                $tabelaExiste = $this->db->query("SHOW TABLES LIKE 'atividades'")->rowCount() > 0;
                error_log("Tabela 'atividades' existe? " . ($tabelaExiste ? 'Sim' : 'Não'));
                if (!$tabelaExiste) {
                    error_log("A tabela 'atividades' não foi encontrada no banco de dados");
                }
            } catch (Exception $e) {
                error_log("Erro ao verificar tabela: " . $e->getMessage());
            }
            return [];
        }
    }

    /**
     * Obtém as atividades de um usuário específico
     * 
     * @param int $usuarioId ID do usuário
     * @param int $limite Número máximo de registros a retornar
     * @return array Lista de atividades do usuário
     */
    public function obterAtividadesPorUsuario($usuarioId, $limite = 20) {
        try {
            $sql = "SELECT * FROM atividades 
                    WHERE id_utilizador = :usuario_id 
                    ORDER BY data_hora DESC 
                    LIMIT :limite";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter atividades do usuário: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpa atividades antigas do banco de dados
     * 
     * @param string $dataLimite Data limite no formato YYYY-MM-DD
     * @return int Número de registros excluídos
     */
    public function limparAtividadesAntigas($dataLimite) {
        try {
            $sql = "DELETE FROM atividades WHERE data_hora < :data_limite";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':data_limite' => $dataLimite]);
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            error_log("Erro ao limpar atividades antigas: " . $e->getMessage());
            return 0;
        }
    }
}
