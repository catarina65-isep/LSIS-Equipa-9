<?php
require_once __DIR__ . '/../DAL/campo_personalizado.php';

class CampoPersonalizadoBLL {
    private $campoDAL;
    private static $instance = null;

    private function __construct() {
        $this->campoDAL = CampoPersonalizadoDAL::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
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
        } elseif ($this->campoDAL->campoExiste($dados['nome'], $id)) {
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
            error_log('=== INÍCIO criarCampo BLL ===');
            error_log('Dados recebidos: ' . print_r($dados, true));
            
            // Valida os dados
            if (!is_array($dados)) {
                error_log('ERRO: Dados não são um array');
                return [
                    'sucesso' => false,
                    'erros' => ['Dados inválidos. Esperado um array.']
                ];
            }
            
            $erros = $this->validarDados($dados);
            if (!empty($erros)) {
                error_log('Erros de validação: ' . print_r($erros, true));
                return [
                    'sucesso' => false,
                    'erros' => $erros
                ];
            }
            
            // Prepara os dados para inserção
            $dadosCampo = [
                'nome' => $dados['nome'] ?? '',
                'tipo' => $dados['tipo'] ?? 'texto',
                'rotulo' => $dados['rotulo'] ?? '',
                'placeholder' => $dados['placeholder'] ?? '',
                'valor_padrao' => $dados['valor_padrao'] ?? '',
                'obrigatorio' => !empty($dados['obrigatorio']) ? 1 : 0,
                'ativo' => (!isset($dados['ativo']) || $dados['ativo'] !== '0') ? 1 : 0,
                'categoria' => $dados['categoria'] ?? 'outros',
                'descricao' => $dados['descricao'] ?? '',
                'dica' => $dados['dica'] ?? '',
                'opcoes' => $dados['opcoes'] ?? [],
                'visivel_para' => $dados['visivel_para'] ?? []
            ];
            
            // Adicionar campos adicionais se existirem
            if (isset($dados['tamanho_maximo'])) {
                $dadosCampo['tamanho_maximo'] = $dados['tamanho_maximo'];
            }
            
            if (isset($dados['secao'])) {
                $dadosCampo['secao'] = $dados['secao'];
            }
            
            if (isset($dados['grupo'])) {
                $dadosCampo['grupo'] = $dados['grupo'];
            }
            
            error_log('Dados preparados para DAL: ' . print_r($dadosCampo, true));
            
            // Insere o campo
            $idCampo = $this->campoDAL->criarCampo($dadosCampo);
            
            if ($idCampo === false) {
                error_log('Erro: criarCampo retornou false');
                throw new Exception('Falha ao criar o campo no banco de dados.');
            }
            
            error_log('Campo criado com sucesso. ID: ' . $idCampo);
            
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
            error_log('=== INÍCIO excluirCampo BLL ===');
            error_log('ID do campo a ser excluído: ' . $id);
            
            // Verifica se o campo existe
            $campoExistente = $this->campoDAL->obterCampoPorId($id);
            if (!$campoExistente) {
                error_log('Campo não encontrado com o ID: ' . $id);
                return [
                    'success' => false,
                    'message' => 'Campo não encontrado.'
                ];
            }
            
            error_log('Campo encontrado. Iniciando exclusão...');
            
            // Exclui o campo
            $resultado = $this->campoDAL->excluirCampo($id);
            
            if ($resultado) {
                error_log('Campo excluído com sucesso. ID: ' . $id);
                return [
                    'success' => true,
                    'message' => 'Campo excluído com sucesso!'
                ];
            } else {
                error_log('Falha ao excluir o campo. ID: ' . $id);
                return [
                    'success' => false,
                    'message' => 'Falha ao excluir o campo.'
                ];
            }
            
        } catch (Exception $e) {
            error_log('Erro ao excluir campo: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao excluir campo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém campos agrupados por categoria
     */
    public function obterCamposPorCategoria($categoria = null, $somenteAtivos = true) {
        try {
            $campos = $this->campoDAL->obterCamposPorCategoria($categoria, $somenteAtivos);
            
            // Decodifica campos JSON
            foreach ($campos as &$campo) {
                if (!empty($campo['opcoes'])) {
                    $campo['opcoes'] = json_decode($campo['opcoes'], true);
                }
                if (!empty($campo['visivel_para'])) {
                    $campo['visivel_para'] = json_decode($campo['visivel_para'], true);
                }
                if (!empty($campo['editavel_por'])) {
                    $campo['editavel_por'] = json_decode($campo['editavel_por'], true);
                }
            }
            
            return [
                'sucesso' => true,
                'dados' => $campos
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao obter campos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém as categorias de campos personalizados
     */
    public function obterCategorias() {
        try {
            $categorias = $this->campoDAL->obterCategorias();
            
            return [
                'sucesso' => true,
                'dados' => $categorias
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao obter categorias: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém o valor de um campo para um colaborador
     */
    public function obterValorCampo($idColaborador, $idCampo) {
        try {
            $valor = $this->campoDAL->obterValorCampo($idColaborador, $idCampo);
            
            return [
                'sucesso' => true,
                'dados' => $valor
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao obter valor do campo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Salva o valor de um campo para um colaborador
     */
    public function salvarValorCampo($idColaborador, $idCampo, $valor, $idUsuario) {
        try {
            // Verifica se o campo existe
            $campo = $this->campoDAL->obterCampoPorId($idCampo);
            if (!$campo) {
                return [
                    'sucesso' => false,
                    'erro' => 'Campo não encontrado.'
                ];
            }
            
            // Validação básica do valor
            if ($campo['obrigatorio'] && empty($valor)) {
                return [
                    'sucesso' => false,
                    'erro' => 'Este campo é obrigatório.'
                ];
            }
            
            // Validações específicas por tipo de campo
            switch ($campo['tipo']) {
                case 'numero':
                    if (!is_numeric($valor) && !empty($valor)) {
                        return [
                            'sucesso' => false,
                            'erro' => 'O valor deve ser um número.'
                        ];
                    }
                    break;
                    
                case 'email':
                    if (!filter_var($valor, FILTER_VALIDATE_EMAIL) && !empty($valor)) {
                        return [
                            'sucesso' => false,
                            'erro' => 'O valor deve ser um e-mail válido.'
                        ];
                    }
                    break;
                    
                // Adicione outras validações conforme necessário
            }
            
            // Salva o valor
            $resultado = $this->campoDAL->salvarValorCampo($idColaborador, $idCampo, $valor, $idUsuario);
            
            return [
                'sucesso' => true,
                'mensagem' => 'Campo salvo com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro ao salvar campo: ' . $e->getMessage()
            ];
        }
    }
}
?>
