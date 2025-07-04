<?php
require_once __DIR__ . '/config.php';

class PerfilAcessoDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Lista todos os perfis de acesso ativos
     * @return array Lista de perfis de acesso
     */
    public function listarTodos() {
        try {
            $sql = "SELECT 
                        id_perfil_acesso as id_perfilacesso, 
                        descricao as nome_perfil, 
                        descricao, 
                        ativo,
                        nivel_acesso,
                        permissoes,
                        data_criacao,
                        CASE 
                            WHEN id_perfil_acesso = 1 THEN 'user-pin' 
                            WHEN id_perfil_acesso = 2 THEN 'user-voice' 
                            WHEN id_perfil_acesso = 3 THEN 'user-check' 
                            WHEN id_perfil_acesso = 4 THEN 'user' 
                            ELSE 'user-circle' 
                        END as icone
                    FROM perfilacesso 
                    WHERE ativo = 1 
                    ORDER BY descricao";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Erro em listarTodos(): ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtém um perfil de acesso pelo ID
     * @param int $id ID do perfil de acesso
     * @return array|null Dados do perfil de acesso ou null se não encontrado
     */
    public function obterPorId($id) {
        try {
            $sql = "SELECT 
                        id_perfil_acesso as id_perfilacesso, 
                        descricao as nome_perfil, 
                        descricao, 
                        ativo,
                        nivel_acesso,
                        permissoes,
                        data_criacao,
                        CASE 
                            WHEN id_perfil_acesso = 1 THEN 'user-pin' 
                            WHEN id_perfil_acesso = 2 THEN 'user-voice' 
                            WHEN id_perfil_acesso = 3 THEN 'user-check' 
                            WHEN id_perfil_acesso = 4 THEN 'user' 
                            ELSE 'user-circle' 
                        END as icone
                    FROM perfilacesso 
                    WHERE id_perfil_acesso = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Erro ao obter perfil de acesso: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Exclui um perfil de acesso pelo ID
     * @param int $id ID do perfil de acesso a ser excluído
     * @return bool True em caso de sucesso, False caso contrário
     */
    public function excluir($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Primeiro, verifica se o perfil existe
            $perfil = $this->obterPorId($id);
            if (!$perfil) {
                throw new Exception('Perfil não encontrado.');
            }
            
            // Exclui o perfil
            $sql = "DELETE FROM perfilacesso WHERE id_perfil_acesso = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            $this->pdo->commit();
            return $resultado;
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Erro ao excluir perfil de acesso: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca um perfil pelo nome
     * @param string $nome Nome do perfil a ser buscado
     * @return array|null Dados do perfil ou null se não encontrado
     */
    public function buscarPorNome($nome) {
        try {
            $sql = "SELECT * FROM perfilacesso WHERE descricao = :nome LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Erro ao buscar perfil por nome: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Insere um novo perfil de acesso
     * @param array $dados Dados do perfil a ser inserido
     * @return int|bool ID do perfil inserido ou false em caso de erro
     */
    public function inserir($dados) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO perfilacesso (
                        descricao, 
                        nivel_acesso, 
                        permissoes, 
                        ativo, 
                        data_criacao
                    ) VALUES (
                        :descricao, 
                        :nivel_acesso, 
                        :permissoes, 
                        :ativo, 
                        NOW()
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            // Usa 'nome_perfil' do array $dados para preencher o campo 'descricao' na tabela
            $stmt->bindParam(':descricao', $dados['nome_perfil'], PDO::PARAM_STR);
            $stmt->bindParam(':nivel_acesso', $dados['nivel_acesso'], PDO::PARAM_INT);
            $stmt->bindParam(':permissoes', $dados['permissoes'], PDO::PARAM_STR);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $id = $this->pdo->lastInsertId();
                $this->pdo->commit();
                return $id;
            } else {
                throw new Exception('Erro ao inserir perfil.');
            }
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Erro ao inserir perfil: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza um perfil de acesso existente
     * @param int $id ID do perfil a ser atualizado
     * @param array $dados Novos dados do perfil
     * @return bool True em caso de sucesso, False caso contrário
     */
    public function atualizar($id, $dados) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "UPDATE perfilacesso SET 
                        descricao = :descricao, 
                        nivel_acesso = :nivel_acesso, 
                        permissoes = :permissoes, 
                        ativo = :ativo,
                        data_atualizacao = NOW()
                    WHERE id_perfil_acesso = :id";
            
            $stmt = $this->pdo->prepare($sql);
            // Usa 'nome_perfil' do array $dados para atualizar o campo 'descricao' na tabela
            $stmt->bindParam(':descricao', $dados['nome_perfil'], PDO::PARAM_STR);
            $stmt->bindParam(':nivel_acesso', $dados['nivel_acesso'], PDO::PARAM_INT);
            $stmt->bindParam(':permissoes', $dados['permissoes'], PDO::PARAM_STR);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            $this->pdo->commit();
            
            return $resultado;
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Erro ao atualizar perfil: ' . $e->getMessage());
            return false;
        }
    }
}
