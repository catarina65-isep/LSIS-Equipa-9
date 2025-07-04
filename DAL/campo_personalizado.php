<?php
require_once __DIR__ . '/conexao.php';

class CampoPersonalizadoDAL {
    private $conn;
    private static $instance = null;

    private function __construct() {
        error_log('=== INÍCIO Construtor CampoPersonalizadoDAL ===');
        try {
            $this->conn = Database::getInstance();
            if ($this->conn) {
                error_log('Conexão com o banco de dados estabelecida com sucesso');
            } else {
                error_log('ERRO: Falha ao obter conexão com o banco de dados');
            }
        } catch (Exception $e) {
            error_log('ERRO no construtor CampoPersonalizadoDAL: ' . $e->getMessage());
            $this->conn = null;
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtém todos os campos personalizados
     */
    public function obterCampos() {
        try {
            $query = "SELECT * FROM campos_personalizados ORDER BY nome";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Campos encontrados: ' . print_r($resultados, true));
            
            return $resultados;
        } catch (PDOException $e) {
            error_log('Erro ao obter campos personalizados: ' . $e->getMessage());
            throw new Exception("Erro ao obter campos personalizados: " . $e->getMessage());
        }
    }

    /**
     * Obtém um campo personalizado pelo ID
     */
    public function obterCampoPorId($id) {
        try {
            $query = "SELECT * FROM campos_personalizados WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao obter campo ID ' . $id . ': ' . $e->getMessage());
            throw new Exception("Erro ao obter campo personalizado: " . $e->getMessage());
        }
    }

    /**
     * Cria um novo campo personalizado
     */
    public function criarCampo($dados) {
        error_log('=== INÍCIO criarCampo DAL ===');
        error_log('Dados recebidos na DAL: ' . print_r($dados, true));
        
        if (!is_array($dados)) {
            error_log('ERRO: Dados não são um array');
            return false;
        }
        
        try {
            // Iniciar transação
            $this->conn->beginTransaction();
            error_log('Transação iniciada');
            
            if (!$this->conn) {
                error_log('ERRO: Conexão com o banco de dados não está ativa');
                return false;
            }
            
            // Preparar dados para a tabela campos_personalizados
            $query = "INSERT INTO campos_personalizados 
                     (nome, tipo, rotulo, descricao, valor_padrao, opcoes, categoria, 
                      obrigatorio, ativo, ordem, requer_comprovativo, visivel_para, data_criacao, data_atualizacao)
                     VALUES 
                     (:nome, :tipo, :rotulo, :descricao, :valor_padrao, :opcoes, :categoria, 
                      :obrigatorio, :ativo, :ordem, :requer_comprovativo, :visivel_para, NOW(), NOW())";
            
            error_log('Query SQL: ' . $query);
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                $error = $this->conn->errorInfo();
                error_log('Erro ao preparar a query: ' . print_r($error, true));
                throw new Exception('Erro ao preparar a query: ' . ($error[2] ?? 'Erro desconhecido'));
            }
            
            // Preparar os valores
            $nome = $dados['nome'] ?? '';
            $tipo = $dados['tipo'] ?? 'texto';
            $rotulo = $dados['rotulo'] ?? '';
            $descricao = $dados['descricao'] ?? '';
            $valorPadrao = $dados['valor_padrao'] ?? '';
            $opcoes = !empty($dados['opcoes']) ? (is_array($dados['opcoes']) ? json_encode($dados['opcoes']) : $dados['opcoes']) : null;
            $categoria = $dados['categoria'] ?? 'outros';
            $obrigatorio = !empty($dados['obrigatorio']) ? 1 : 0;
            $ativo = !isset($dados['ativo']) || $dados['ativo'] ? 1 : 0;
            $ordem = $dados['ordem'] ?? 0;
            $requerComprovativo = !empty($dados['requer_comprovativo']) ? 1 : 0;
            $visivelPara = !empty($dados['visivel_para']) ? (is_array($dados['visivel_para']) ? json_encode($dados['visivel_para']) : $dados['visivel_para']) : '[]';
            
            // Vincular parâmetros
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':rotulo', $rotulo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':valor_padrao', $valorPadrao);
            $stmt->bindParam(':opcoes', $opcoes);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':obrigatorio', $obrigatorio, PDO::PARAM_INT);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
            $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
            $stmt->bindParam(':requer_comprovativo', $requerComprovativo, PDO::PARAM_INT);
            $stmt->bindParam(':visivel_para', $visivelPara);
            
            error_log('Parâmetros: ' . print_r([
                'nome' => $nome,
                'tipo' => $tipo,
                'rotulo' => $rotulo,
                'categoria' => $categoria,
                'obrigatorio' => $obrigatorio,
                'ativo' => $ativo
            ], true));
            
            error_log('Executando a query...');
            $resultado = $stmt->execute();
            
            if ($resultado === false) {
                $erroInfo = $stmt->errorInfo();
                error_log('Erro ao executar a query: ' . print_r($erroInfo, true));
                throw new Exception('Erro ao executar a query: ' . ($erroInfo[2] ?? 'Erro desconhecido'));
            }
            
            $idInserido = $this->conn->lastInsertId();
            error_log('Campo criado com sucesso. ID: ' . $idInserido);
            
            // Fazer commit da transação
            if ($this->conn->commit()) {
                error_log('Commit realizado com sucesso');
                return $idInserido;
            } else {
                error_log('ERRO: Falha ao fazer commit da transação');
                return false;
            }
        } catch (PDOException $e) {
            // Fazer rollback em caso de erro
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
                error_log('Rollback realizado devido a erro: ' . $e->getMessage());
            }
            throw new Exception("Erro ao criar campo personalizado: " . $e->getMessage());
        }
    }

    /**
     * Atualiza um campo personalizado existente
     */
    public function atualizarCampo($id, $dados) {
        try {
            error_log('=== INÍCIO atualizarCampo DAL ===');
            error_log('Atualizando campo ID: ' . $id);
            error_log('Dados recebidos: ' . print_r($dados, true));
            
            $query = "UPDATE campos_personalizados SET 
                     tipo = :tipo, 
                     rotulo = :rotulo, 
                     categoria = :categoria, 
                     valor_padrao = :valor_padrao, 
                     opcoes = :opcoes,
                     obrigatorio = :obrigatorio, 
                     ativo = :ativo, 
                     requer_comprovativo = :requer_comprovativo,
                     visivel_para = :visivel_para,
                     data_atualizacao = NOW()
                     WHERE id = :id";
            
            error_log('Query SQL: ' . $query);
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                $error = $this->conn->errorInfo();
                error_log('Erro ao preparar a query: ' . print_r($error, true));
                throw new Exception('Erro ao preparar a query: ' . ($error[2] ?? 'Erro desconhecido'));
            }
            
            // Preparar os valores
            $opcoes = !empty($dados['opcoes']) ? (is_array($dados['opcoes']) ? json_encode($dados['opcoes']) : $dados['opcoes']) : null;
            $visivelPara = !empty($dados['visivel_para']) ? (is_array($dados['visivel_para']) ? json_encode($dados['visivel_para']) : $dados['visivel_para']) : '[]';
            
            // Vincular parâmetros
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $dados['tipo']);
            $stmt->bindParam(':rotulo', $dados['rotulo']);
            $stmt->bindParam(':categoria', $dados['categoria']);
            $stmt->bindParam(':valor_padrao', $dados['valor_padrao']);
            $stmt->bindParam(':opcoes', $opcoes);
            $stmt->bindParam(':obrigatorio', $dados['obrigatorio'], PDO::PARAM_INT);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_INT);
            $stmt->bindParam(':requer_comprovativo', $dados['requer_comprovativo'], PDO::PARAM_INT);
            $stmt->bindParam(':visivel_para', $visivelPara);
            
            error_log('Parâmetros: ' . print_r([
                'id' => $id,
                'tipo' => $dados['tipo'],
                'rotulo' => $dados['rotulo'],
                'categoria' => $dados['categoria'],
                'obrigatorio' => $dados['obrigatorio'],
                'ativo' => $dados['ativo']
            ], true));
            
            $resultado = $stmt->execute();
            
            if ($resultado === false) {
                $erroInfo = $stmt->errorInfo();
                error_log('Erro ao executar a query: ' . print_r($erroInfo, true));
                throw new Exception('Erro ao executar a query: ' . ($erroInfo[2] ?? 'Erro desconhecido'));
            }
            
            error_log('Campo atualizado com sucesso. Linhas afetadas: ' . $stmt->rowCount());
            return $resultado;
            
        } catch (PDOException $e) {
            error_log('Erro ao atualizar campo: ' . $e->getMessage());
            throw new Exception("Erro ao atualizar campo personalizado: " . $e->getMessage());
        }
    }

    /**
     * Exclui um campo personalizado
     */
    public function excluirCampo($id) {
        try {
            error_log('=== INÍCIO excluirCampo DAL ===');
            error_log('Excluindo campo ID: ' . $id);
            
            $query = "DELETE FROM campos_personalizados WHERE id = :id";
            error_log('Query SQL: ' . $query);
            
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                $error = $this->conn->errorInfo();
                error_log('Erro ao preparar a query: ' . print_r($error, true));
                throw new Exception('Erro ao preparar a query: ' . ($error[2] ?? 'Erro desconhecido'));
            }
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            $linhasAfetadas = $stmt->rowCount();
            
            error_log('Exclusão do campo ID ' . $id . ' - ' . ($resultado ? 'Sucesso' : 'Falha'));
            error_log('Linhas afetadas: ' . $linhasAfetadas);
            
            if ($linhasAfetadas === 0) {
                error_log('Nenhum registro encontrado com o ID: ' . $id);
                return false;
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log('Erro ao excluir campo personalizado: ' . $e->getMessage());
            throw new Exception("Erro ao excluir campo personalizado: " . $e->getMessage());
        }
    }

    /**
     * Verifica se um campo com o nome informado já existe
     */
    public function campoExiste($nome, $id = null) {
        try {
            $query = "SELECT COUNT(*) FROM campos_personalizados WHERE nome = :nome";
            
            if ($id) {
                $query .= " AND id != :id";
            }
            
            error_log('Verificando se campo existe - Query: ' . $query);
            error_log('Parâmetros - nome: ' . $nome . ', id: ' . $id);
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome', $nome);
            
            if ($id) {
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            error_log("Verificando se campo '$nome' existe (ID: $id): $count registros encontrados");
            
            return $count > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar existência do campo: " . $e->getMessage());
        }
    }
}
?>
