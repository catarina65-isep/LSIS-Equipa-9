<?php
require_once __DIR__ . '/../DAL/campo_personalizado.php';

class CampoPersonalizadoBLL {
    private $campoDAL;

    public function __construct() {
        $this->campoDAL = new CampoPersonalizadoDAL();
    }

    /**
     * Valida os dados de um campo personalizado
     */
    private function validarDados($dados, $id = null) {
        $erros = [];
        
        // Validação do nome
        if (empty($dados['nome'])) {
            $erros[] = "O nome do campo é obrigatório.";
        } elseif (!preg_match('/^[a-z0-9_]+$/', $dados['nome'])) {
            $erros[] = "O nome do campo deve conter apenas letras minúsculas, números e sublinhados.";
        } elseif ($this->campoDAL->nomeExiste($dados['nome'], $id)) {
            $erros[] = "Já existe um campo com este nome.";
        }
        
        // Validação do tipo
        $tiposPermitidos = [
            'texto', 'numero', 'data', 'email', 'telefone', 'cep', 'cpf', 'cnpj', 
            'select', 'checkbox', 'radio', 'textarea', 'arquivo', 'mod99', 'nif', 
            'niss', 'cartaocidadao'
        ];
        
        if (empty($dados['tipo']) || !in_array($dados['tipo'], $tiposPermitidos)) {
            $erros[] = "Tipo de campo inválido.";
        }
        
        // Validação do rótulo
        if (empty($dados['rotulo'])) {
            $erros[] = "O rótulo do campo é obrigatório.";
        }
        
        // Validação de opções para campos de seleção
        if (in_array($dados['tipo'], ['select', 'radio', 'checkbox']) && empty($dados['opcoes'])) {
            $erros[] = "Campos do tipo " . $dados['tipo'] . " requerem opções.";
        }
        
        return $erros;
    }

    /**
     * Obtém todos os campos personalizados
     */
    public function obterCampos() {
        try {
            return [
                'sucesso' => true,
                'dados' => $this->campoDAL->obterCampos()
            ];
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao obter campos personalizados: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém um campo personalizado pelo ID
     */
    public function obterCampoPorId($id) {
        try {
            $campo = $this->campoDAL->obterCampoPorId($id);
            
            if (!$campo) {
                return [
                    'sucesso' => false,
                    'erro' => 'Campo não encontrado.'
                ];
            }
            
            // Decodifica os campos JSON
            if (!empty($campo['opcoes'])) {
                $campo['opcoes'] = json_decode($campo['opcoes'], true);
            }
            if (!empty($campo['visivel_para'])) {
                $campo['visivel_para'] = json_decode($campo['visivel_para'], true);
            }
            if (!empty($campo['editavel_por'])) {
                $campo['editavel_por'] = json_decode($campo['editavel_por'], true);
            }
            
            return [
                'sucesso' => true,
                'dados' => $campo
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao obter campo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cria um novo campo personalizado
     */
    public function criarCampo($dados) {
        try {
            // Valida os dados
            $erros = $this->validarDados($dados);
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros
                ];
            }
            
            // Prepara os dados para inserção
            $dadosCampo = [
                'nome' => $dados['nome'],
                'tipo' => $dados['tipo'],
                'rotulo' => $dados['rotulo'],
                'placeholder' => $dados['placeholder'] ?? '',
                'valor_padrao' => $dados['valor_padrao'] ?? '',
                'obrigatorio' => !empty($dados['obrigatorio']),
                'ativo' => !isset($dados['ativo']) || $dados['ativo'] !== '0',
                'categoria' => $dados['categoria'] ?? 'outros',
                'requer_comprovativo' => !empty($dados['requer_comprovativo']),
                'visivel_para' => $dados['visivel_para'] ?? ['admin'],
                'editavel_por' => $dados['editavel_por'] ?? ['admin'],
                'ajuda' => $dados['ajuda'] ?? '',
                'opcoes' => $dados['opcoes'] ?? []
            ];
            
            // Insere o campo
            $idCampo = $this->campoDAL->criarCampo($dadosCampo);
            
            return [
                'sucesso' => true,
                'id' => $idCampo,
                'mensagem' => 'Campo criado com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao criar campo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Atualiza um campo personalizado existente
     */
    public function atualizarCampo($id, $dados) {
        try {
            // Verifica se o campo existe
            $campoExistente = $this->campoDAL->obterCampoPorId($id);
            if (!$campoExistente) {
                return [
                    'sucesso' => false,
                    'erro' => 'Campo não encontrado.'
                ];
            }
            
            // Valida os dados
            $erros = $this->validarDados($dados, $id);
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros
                ];
            }
            
            // Prepara os dados para atualização
            $dadosCampo = [
                'nome' => $dados['nome'],
                'tipo' => $dados['tipo'],
                'rotulo' => $dados['rotulo'],
                'placeholder' => $dados['placeholder'] ?? '',
                'valor_padrao' => $dados['valor_padrao'] ?? '',
                'obrigatorio' => !empty($dados['obrigatorio']),
                'ativo' => !isset($dados['ativo']) || $dados['ativo'] !== '0',
                'categoria' => $dados['categoria'] ?? 'outros',
                'requer_comprovativo' => !empty($dados['requer_comprovativo']),
                'visivel_para' => $dados['visivel_para'] ?? ['admin'],
                'editavel_por' => $dados['editavel_por'] ?? ['admin'],
                'ajuda' => $dados['ajuda'] ?? '',
                'opcoes' => $dados['opcoes'] ?? []
            ];
            
            // Atualiza o campo
            $resultado = $this->campoDAL->atualizarCampo($id, $dadosCampo);
            
            if (!$resultado) {
                throw new Exception('Nenhuma alteração foi feita.');
            }
            
            return [
                'sucesso' => true,
                'mensagem' => 'Campo atualizado com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao atualizar campo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Exclui um campo personalizado
     */
    public function excluirCampo($id) {
        try {
            // Verifica se o campo existe
            $campoExistente = $this->campoDAL->obterCampoPorId($id);
            if (!$campoExistente) {
                return [
                    'sucesso' => false,
                    'erro' => 'Campo não encontrado.'
                ];
            }
            
            // Exclui o campo
            $resultado = $this->campoDAL->excluirCampo($id);
            
            return [
                'sucesso' => true,
                'mensagem' => 'Campo excluído com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao excluir campo: ' . $e->getMessage()
            ];
        }
    }
}
?>
