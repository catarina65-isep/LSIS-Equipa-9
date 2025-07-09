<?php
require_once __DIR__ . '/config.php';

class UtilizadorDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    /**
     * Obtém a instância PDO
     * 
     * @return \PDO
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Conta o número total de usuários ativos
     * 
     * @return int Número total de usuários ativos
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM utilizador WHERE ativo = 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    
    /**
     * Conta usuários criados em um período específico
     * 
     * @param string $dataInicio Data de início (YYYY-MM-DD)
     * @param string $dataFim Data de fim (YYYY-MM-DD)
     * @return int Número de usuários criados no período
     */
    public function contarPorPeriodo($dataInicio, $dataFim) {
        $sql = "SELECT COUNT(*) as total 
                FROM utilizador 
                WHERE data_criacao BETWEEN :data_inicio AND :data_fim";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
    
    /**
     * Obtém a distribuição de usuários por perfil
     * 
     * @return array Distribuição de usuários por perfil
     */
    /**
     * Lista todos os usuários visíveis para o RH (exceto administradores)
     * 
     * @return array Lista de usuários
     */
    public function listarUtilizadoresRH() {
        try {
            // Consulta simplificada que não depende da estrutura da tabela perfilacesso
            $sql = "SELECT 
                        u.*,
                        CASE 
                            WHEN u.id_perfilacesso = 1 THEN 'Administrador'
                            WHEN u.id_perfilacesso = 2 THEN 'Recursos Humanos'
                            WHEN u.id_perfilacesso = 3 THEN 'Gestor'
                            WHEN u.id_perfilacesso = 4 THEN 'Colaborador'
                            ELSE 'Perfil ' . u.id_perfilacesso
                        END as perfil_nome
                    FROM utilizador u 
                    WHERE u.id_perfilacesso != 1  -- Exclui administradores
                    ORDER BY u.nome ASC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Em caso de erro, logar o erro e retornar um array vazio
            error_log("Erro em listarUtilizadoresRH: " . $e->getMessage());
            return [];
        }
    }
    

    
    /**
     * Obtém a distribuição de usuários por perfil
     * 
     * @return array Distribuição de usuários por perfil
     */
    public function obterDistribuicaoPorPerfil() {
        $sql = "SELECT 
                    p.nome as perfil,
                    COUNT(u.id_utilizador) as total,
                    ROUND((COUNT(u.id_utilizador) * 100.0) / (SELECT COUNT(*) FROM utilizador WHERE ativo = 1), 2) as percentual
                FROM perfilacesso p
                LEFT JOIN utilizador u ON p.id_perfilacesso = u.id_perfilacesso AND u.ativo = 1
                GROUP BY p.id_perfilacesso, p.nome
                ORDER BY total DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Conta usuários por equipe
     * 
     * @return array Contagem de usuários por equipe
     */
    public function contarUsuariosPorEquipa() {
        $sql = "SELECT 
                    e.nome as equipa,
                    COUNT(DISTINCT u.id_utilizador) as total_usuarios,
                    COUNT(DISTINCT c.id_colaborador) as total_colaboradores
                FROM equipa e
                LEFT JOIN equipa_membros em ON e.id_equipa = em.equipa_id
                LEFT JOIN utilizador u ON em.utilizador_id = u.id_utilizador
                LEFT JOIN colaborador c ON em.utilizador_id = c.id_utilizador
                WHERE e.ativo = 1
                GROUP BY e.id_equipa, e.nome
                ORDER BY total_usuarios DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Conta quantos usuários estão associados a um perfil específico
     * @param int $idPerfil ID do perfil de acesso
     * @return int Número de usuários associados ao perfil
     */
    public function contarPorPerfil($idPerfil) {
        try {
            $sql = "SELECT COUNT(*) as total FROM utilizador 
                    WHERE id_perfilacesso = :id_perfilacesso AND ativo = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_perfilacesso', $idPerfil, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
            
        } catch (Exception $e) {
            error_log('Erro ao contar usuários por perfil: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lista usuários por perfil
     * @param int $idPerfil ID do perfil de acesso
     * @return array Lista de usuários
     */
    public function listarPorPerfil($idPerfil) {
        try {
            // Log temporário para depuração
            error_log('Buscando usuários por perfil. ID Perfil: ' . $idPerfil);
            
            // Verificar a estrutura da tabela
            $checkTable = $this->pdo->query("SHOW COLUMNS FROM utilizador LIKE 'id_perfil%'");
            $columns = $checkTable->fetchAll(PDO::FETCH_COLUMN);
            error_log('Colunas encontradas na tabela utilizador: ' . print_r($columns, true));
            $sql = "SELECT 
                        u.id_utilizador as id,
                        COALESCE(CONCAT(c.nome, ' ', c.apelido), u.username) as nome,
                        u.email,
                        u.username,
                        u.ativo,
                        c.foto
                    FROM utilizador u
                    LEFT JOIN colaborador c ON u.id_colaborador = c.id_colaborador
                    WHERE u.id_perfil_acesso = :id_perfilacesso 
                    AND u.ativo = 1
                    AND u.username != 'rh'  -- Remove apenas o usuário genérico 'rh'
                    ORDER BY COALESCE(c.nome, u.username)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_perfilacesso', $idPerfil, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Erro ao listar usuários por perfil: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista todos os usuários ativos
     * @return array Lista de usuários ativos
     */
    public function listarAtivos() {
        try {
            $sql = "SELECT 
                        u.id_utilizador as id,
                        c.nome,
                        c.apelido,
                        u.username,
                        CONCAT(c.nome, ' ', c.apelido) as nome_completo,
                        u.email,
                        c.foto
                    FROM utilizador u
                    INNER JOIN colaborador c ON u.id_utilizador = c.id_utilizador
                    WHERE u.ativo = 1
                    AND u.id_utilizador IS NOT NULL
                    AND u.id_utilizador NOT IN (
                        SELECT id_utilizador 
                        FROM utilizador 
                        WHERE username = 'admin' 
                        OR email = 'admin@tlantic.pt'
                        OR username = 'rh' 
                        OR username = 'coordenador'
                    )
                    ORDER BY c.nome, c.apelido";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para depuração
            error_log('Resultados da consulta listarAtivos: ' . print_r($resultados, true));
            
            return $resultados;
            
        } catch (Exception $e) {
            error_log('Erro ao listar usuários ativos: ' . $e->getMessage());
            return [];
        }
    }

    public function listarTodos() {
        try {
            // Primeiro, tenta a consulta com JOIN na tabela colaborador (singular)
            try {
                $sql = "SELECT 
                            u.id_utilizador, 
                            COALESCE(c.nome, u.username) as nome, 
                            u.email, 
                            u.username, 
                            u.ativo, 
                            u.data_criacao,
                            u.id_colaborador
                        FROM utilizador u
                        LEFT JOIN colaborador c ON u.id_colaborador = c.id_colaborador
                        WHERE u.ativo = 1 
                        ORDER BY COALESCE(c.nome, u.username)";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($resultados)) {
                    return $resultados;
                }
                
            } catch (Exception $e) {
                error_log('Erro na consulta com JOIN, tentando sem JOIN: ' . $e->getMessage());
            }
            
            // Se chegou aqui, a consulta com JOIN falhou ou não retornou resultados
            // Tenta uma consulta mais simples apenas com a tabela utilizador
            $sql = "SELECT 
                        id_utilizador, 
                        username as nome, 
                        email, 
                        username, 
                        ativo, 
                        data_criacao,
                        id_colaborador
                    FROM utilizador 
                    WHERE ativo = 1 
                    ORDER BY username";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultados;
            
        } catch (Exception $e) {
            error_log('Erro em listarTodos(): ' . $e->getMessage());
            throw $e;
        }
    }

    public function obterPorId($id) {
        try {
            // Primeiro, tenta buscar com JOIN na tabela colaborador (singular)
            try {
                $sql = "SELECT 
                            u.id_utilizador, 
                            COALESCE(c.nome, u.username) as nome, 
                            u.email, 
                            u.username, 
                            u.ativo, 
                            u.data_criacao,
                            u.id_utilizador,
                            u.id_perfil_acesso
                        FROM utilizador u
                        LEFT JOIN colaborador c ON u.id_utilizador = c.id_utilizador

                        WHERE u.id_utilizador = :id";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($resultado) {
                    // Garante que id_perfil_acesso esteja definido, mesmo que como nulo
                    if (!isset($resultado['id_perfil_acesso']) && !isset($resultado['id_perfilacesso'])) {
                        error_log("Aviso: Nenhum campo de perfil de acesso encontrado para o usuário ID: $id");
                    }
                    
                    // Se id_perfil_acesso não estiver definido, mas id_perfilacesso estiver, copia o valor
                    if (!isset($resultado['id_perfil_acesso']) && isset($resultado['id_perfilacesso'])) {
                        $resultado['id_perfil_acesso'] = $resultado['id_perfilacesso'];
                    }
                    
                    // Se id_perfilacesso não estiver definido, mas id_perfil_acesso estiver, copia o valor
                    if (!isset($resultado['id_perfilacesso']) && isset($resultado['id_perfil_acesso'])) {
                        $resultado['id_perfilacesso'] = $resultado['id_perfil_acesso'];
                    }
                    
                    error_log("Dados do usuário retornados: " . print_r($resultado, true));
                    return $resultado;
                }
                
            } catch (Exception $e) {
                error_log('Erro na consulta com JOIN, tentando sem JOIN: ' . $e->getMessage());
            }
            
            // Se chegou aqui, a consulta com JOIN falhou ou não retornou resultados
            // Tenta uma consulta mais simples apenas com a tabela utilizador
            $sql = "SELECT 
                        id_utilizador, 
                        username as nome, 
                        email, 
                        username, 
                        ativo, 
                        data_criacao,
                        id_colaborador,
                        id_perfil_acesso,
                        id_perfilacesso
                    FROM utilizador 
                    WHERE id_utilizador = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                // Garante que id_perfil_acesso esteja definido, mesmo que como nulo
                if (!isset($resultado['id_perfil_acesso']) && !isset($resultado['id_perfilacesso'])) {
                    error_log("Aviso: Nenhum campo de perfil de acesso encontrado para o usuário ID: $id (consulta simples)");
                }
                
                // Se id_perfil_acesso não estiver definido, mas id_perfilacesso estiver, copia o valor
                if (!isset($resultado['id_perfil_acesso']) && isset($resultado['id_perfilacesso'])) {
                    $resultado['id_perfil_acesso'] = $resultado['id_perfilacesso'];
                }
                
                // Se id_perfilacesso não estiver definido, mas id_perfil_acesso estiver, copia o valor
                if (!isset($resultado['id_perfilacesso']) && isset($resultado['id_perfil_acesso'])) {
                    $resultado['id_perfilacesso'] = $resultado['id_perfil_acesso'];
                }
                
                error_log("Dados do usuário retornados (consulta simples): " . print_r($resultado, true));
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log('Erro em obterPorId(): ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Atualiza os dados de um utilizador
     * @param array $dados Dados do utilizador a serem atualizados
     * @return bool True em caso de sucesso, False caso contrário
     */
    public function atualizar($dados) {
        try {
            error_log('Iniciando atualização do usuário: ' . print_r($dados, true));
            
            // Iniciar transação
            $this->pdo->beginTransaction();
            
            // Primeiro, verificar se a coluna 'nome' existe na tabela
            $colunaNomeExiste = false;
            try {
                $checkColumn = $this->pdo->query("SHOW COLUMNS FROM utilizador LIKE 'nome'");
                $colunaNomeExiste = ($checkColumn->rowCount() > 0);
            } catch (Exception $e) {
                error_log('Erro ao verificar coluna nome: ' . $e->getMessage());
            }
            
            // Construir a query de atualização
            $sql = "UPDATE utilizador SET 
                        username = :username, 
                        email = :email, 
                        id_perfilacesso = :id_perfilacesso, 
                        ativo = :ativo";
                        
            // Adicionar o campo nome apenas se a coluna existir
            if ($colunaNomeExiste) {
                $sql .= ", nome = :nome";
                error_log('Coluna nome encontrada, será atualizada');
            } else {
                error_log('Coluna nome não encontrada na tabela utilizador');
            }
            
            error_log('Query SQL base: ' . $sql);
            
            // Adicionar a senha à query se for fornecida
            if (!empty($dados['password_hash'])) {
                $sql .= ", password_hash = :password_hash";
                error_log('Senha fornecida, adicionando ao update');
            }
            
            // Adicionar a cláusula WHERE
            $sql .= " WHERE id_utilizador = :id_utilizador";
            
            // Preparar a declaração
            $stmt = $this->pdo->prepare($sql);
            
            // Bind dos parâmetros
            $stmt->bindValue(':username', $dados['username'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $dados['email'], PDO::PARAM_STR);
            $stmt->bindValue(':id_perfilacesso', $dados['id_perfilacesso'], PDO::PARAM_INT);
            $stmt->bindValue(':ativo', $dados['ativo'] ?? 1, PDO::PARAM_INT);
            
            // Bind do nome apenas se a coluna existir
            if ($colunaNomeExiste) {
                $stmt->bindValue(':nome', $dados['nome'] ?? $dados['username'], PDO::PARAM_STR);
            }
            
            // Bind da senha se for fornecida
            if (!empty($dados['password_hash'])) {
                $stmt->bindValue(':password_hash', $dados['password_hash'], PDO::PARAM_STR);
            }
            
            $stmt->bindValue(':id_utilizador', $dados['id_utilizador'], PDO::PARAM_INT);
            
            // Executar a atualização
            $resultado = $stmt->execute();
            
            // Se o usuário tiver um id_colaborador, atualizar também o nome na tabela colaborador
            if ($resultado && !empty($dados['id_colaborador'])) {
                try {
                    error_log('Atualizando informações do colaborador: ' . $dados['id_colaborador']);
                    
                    // Verificar se a tabela colaborador tem a coluna nome
                    $checkColaboradorNome = $this->pdo->query("SHOW COLUMNS FROM colaborador LIKE 'nome'");
                    $colunaColaboradorNomeExiste = ($checkColaboradorNome->rowCount() > 0);
                    
                    if ($colunaColaboradorNomeExiste) {
                        $sqlColaborador = "UPDATE colaborador SET nome = :nome WHERE id_colaborador = :id_colaborador";
                        error_log('Query de atualização do colaborador: ' . $sqlColaborador);
                        error_log('Dados do colaborador - Nome: ' . $dados['nome'] . ', ID: ' . $dados['id_colaborador']);
                        
                        $stmtColaborador = $this->pdo->prepare($sqlColaborador);
                        $stmtColaborador->bindValue(':nome', $dados['nome'], PDO::PARAM_STR);
                        $stmtColaborador->bindValue(':id_colaborador', $dados['id_colaborador'], PDO::PARAM_INT);
                        $resultadoColaborador = $stmtColaborador->execute();
                        error_log('Resultado da atualização do colaborador: ' . ($resultadoColaborador ? 'Sucesso' : 'Falha'));
                        
                        if (!$resultadoColaborador) {
                            $errorInfo = $stmtColaborador->errorInfo();
                            error_log('Erro ao atualizar colaborador: ' . print_r($errorInfo, true));
                        }
                    } else {
                        error_log('A coluna nome não existe na tabela colaborador');
                    }
                } catch (Exception $e) {
                    error_log('Erro ao tentar atualizar tabela colaborador: ' . $e->getMessage());
                }
            } else {
                error_log('Nenhum ID de colaborador fornecido ou atualização do usuário falhou');
                error_log('Dados recebidos: ' . print_r($dados, true));
            }
            
            // Confirmar a transação
            $this->pdo->commit();
            
            return $resultado;
            
        } catch (Exception $e) {
            // Em caso de erro, desfazer a transação
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log('Erro ao atualizar utilizador: ' . $e->getMessage());
            throw $e;
        }
    }
}
