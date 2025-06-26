<?php
require_once __DIR__ . '/database.php';

class CampoPersonalizadoDAL {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtém todos os campos personalizados
     */
    public function obterCampos() {
        $query = "SELECT * FROM campo_personalizado ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém um campo personalizado pelo ID
     */
    public function obterCampoPorId($id) {
        $query = "SELECT * FROM campo_personalizado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo campo personalizado
     */
    public function criarCampo($dados) {
        try {
            $this->conn->beginTransaction();

            // Prepara os dados
            $opcoes = !empty($dados['opcoes']) ? json_encode($dados['opcoes']) : null;
            $visivelPara = is_array($dados['visivel_para']) ? json_encode($dados['visivel_para']) : $dados['visivel_para'];
            $editavelPor = is_array($dados['editavel_por']) ? json_encode($dados['editavel_por']) : $dados['editavel_por'];
            
            $query = "
                INSERT INTO campo_personalizado (
                    nome, tipo, rotulo, placeholder, valor_padrao, 
                    obrigatorio, ativo, categoria, requer_comprovativo, 
                    visivel_para, editavel_por, ajuda, opcoes
                ) VALUES (
                    :nome, :tipo, :rotulo, :placeholder, :valor_padrao,
                    :obrigatorio, :ativo, :categoria, :requer_comprovativo,
                    :visivel_para, :editavel_por, :ajuda, :opcoes
                )
            ";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            $stmt->bindParam(':rotulo', $dados['rotulo']);
            $stmt->bindParam(':placeholder', $dados['placeholder']);
            $stmt->bindParam(':valor_padrao', $dados['valor_padrao']);
            $stmt->bindParam(':obrigatorio', $dados['obrigatorio'], PDO::PARAM_BOOL);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':categoria', $dados['categoria']);
            $stmt->bindParam(':requer_comprovativo', $dados['requer_comprovativo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':visivel_para', $visivelPara);
            $stmt->bindParam(':editavel_por', $editavelPor);
            $stmt->bindParam(':ajuda', $dados['ajuda']);
            $stmt->bindParam(':opcoes', $opcoes);
            
            $stmt->execute();
            $idCampo = $this->conn->lastInsertId();
            
            $this->conn->commit();
            return $idCampo;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza um campo personalizado existente
     */
    public function atualizarCampo($id, $dados) {
        try {
            $this->conn->beginTransaction();

            // Prepara os dados
            $opcoes = !empty($dados['opcoes']) ? json_encode($dados['opcoes']) : null;
            $visivelPara = is_array($dados['visivel_para']) ? json_encode($dados['visivel_para']) : $dados['visivel_para'];
            $editavelPor = is_array($dados['editavel_por']) ? json_encode($dados['editavel_por']) : $dados['editavel_por'];
            
            $query = "
                UPDATE campo_personalizado SET
                    nome = :nome,
                    tipo = :tipo,
                    rotulo = :rotulo,
                    placeholder = :placeholder,
                    valor_padrao = :valor_padrao,
                    obrigatorio = :obrigatorio,
                    ativo = :ativo,
                    categoria = :categoria,
                    requer_comprovativo = :requer_comprovativo,
                    visivel_para = :visivel_para,
                    editavel_por = :editavel_por,
                    ajuda = :ajuda,
                    opcoes = :opcoes,
                    atualizado_em = NOW()
                WHERE id = :id
            ";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            $stmt->bindParam(':rotulo', $dados['rotulo']);
            $stmt->bindParam(':placeholder', $dados['placeholder']);
            $stmt->bindParam(':valor_padrao', $dados['valor_padrao']);
            $stmt->bindParam(':obrigatorio', $dados['obrigatorio'], PDO::PARAM_BOOL);
            $stmt->bindParam(':ativo', $dados['ativo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':categoria', $dados['categoria']);
            $stmt->bindParam(':requer_comprovativo', $dados['requer_comprovativo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':visivel_para', $visivelPara);
            $stmt->bindParam(':editavel_por', $editavelPor);
            $stmt->bindParam(':ajuda', $dados['ajuda']);
            $stmt->bindParam(':opcoes', $opcoes);
            
            $result = $stmt->execute();
            
            $this->conn->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Exclui um campo personalizado
     */
    public function excluirCampo($id) {
        try {
            $this->conn->beginTransaction();
            
            // Primeiro, verifica se existem valores associados
            $query = "SELECT COUNT(*) as total FROM valor_campo_personalizado WHERE id_campo = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                throw new Exception("Não é possível excluir este campo pois existem valores associados a ele.");
            }
            
            // Se não houver valores associados, exclui o campo
            $query = "DELETE FROM campo_personalizado WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            $this->conn->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Verifica se um nome de campo já existe
     */
    public function nomeExiste($nome, $id = null) {
        $query = "SELECT COUNT(*) as total FROM campo_personalizado WHERE nome = :nome";
        $params = [':nome' => $nome];
        
        if ($id !== null) {
            $query .= " AND id != :id";
            $params[':id'] = $id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0;
    }
}
?>
