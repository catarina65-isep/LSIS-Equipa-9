<?php
require_once __DIR__ . '/../DAL/UtilizadorDAL.php';

class UtilizadorBLL {
    private $utilizadorDAL;

    public function __construct() {
        $this->utilizadorDAL = new UtilizadorDAL();
    }
    
    /**
     * Conta quantos usuários estão associados a um perfil específico
     * @param int $idPerfil ID do perfil de acesso
     * @return int Número de usuários associados ao perfil
     */
    public function contarPorPerfil($idPerfil) {
        try {
            if (!is_numeric($idPerfil) || $idPerfil <= 0) {
                throw new Exception('ID de perfil inválido');
            }
            
            return $this->utilizadorDAL->contarPorPerfil($idPerfil);
            
        } catch (Exception $e) {
            error_log('Erro ao contar usuários por perfil: ' . $e->getMessage());
            return 0;
        }
    }

    public function listarTodos() {
        try {
            return $this->utilizadorDAL->listarTodos();
        } catch (Exception $e) {
            error_log('Erro ao listar usuários: ' . $e->getMessage());
            return [];
        }
    }

    public function obterPorId($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID de usuário inválido');
            }
            
            return $this->utilizadorDAL->obterPorId($id);
        } catch (Exception $e) {
            error_log('Erro ao obter usuário: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém a lista de coordenadores (usuários com perfil de coordenador)
     * @return array Lista de coordenadores
     */
    public function obterCoordenadores() {
        try {
            // Assumindo que o perfil de coordenador tem ID 2
            // Ajuste o ID conforme sua estrutura de perfis
            return $this->utilizadorDAL->listarPorPerfil(2);
        } catch (Exception $e) {
            error_log('Erro ao obter coordenadores: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém a lista de funcionários (usuários ativos que podem ser membros de equipe)
     * @return array Lista de funcionários
     */
    public function obterFuncionarios() {
        try {
            // Ajuste os critérios conforme necessário
            return $this->utilizadorDAL->listarAtivos();
        } catch (Exception $e) {
            error_log('Erro ao obter funcionários: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lista todos os usuários ativos
     * @return array Lista de usuários ativos
     */
    public function listarUtilizadoresAtivos() {
        try {
            // A DAL já filtra por usuários ativos
            return $this->utilizadorDAL->listarTodos();
        } catch (Exception $e) {
            error_log('Erro ao listar usuários ativos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualiza os dados de um usuário
     * @param array $dados Dados do usuário a serem atualizados
     * @return bool True em caso de sucesso, False caso contrário
     */
    public function atualizar($dados) {
        try {
            // Validar dados obrigatórios
            $camposObrigatorios = ['id_utilizador', 'nome', 'email', 'username', 'id_perfilacesso'];
            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo]) || $dados[$campo] === '') {
                    throw new Exception("O campo {$campo} é obrigatório.");
                }
            }
            
            // Validar e-mail
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('O endereço de e-mail não é válido.');
            }
            
            // Verificar se o usuário existe
            $usuarioExistente = $this->obterPorId($dados['id_utilizador']);
            if (!$usuarioExistente) {
                throw new Exception('Usuário não encontrado.');
            }
            
            // Obter o usuário existente para pegar o id_colaborador se não estiver nos dados
            $usuarioExistente = $this->obterPorId($dados['id_utilizador']);
            error_log('Dados do usuário existente: ' . print_r($usuarioExistente, true));
            
            if ($usuarioExistente && !empty($usuarioExistente['id_colaborador'])) {
                $dados['id_colaborador'] = $usuarioExistente['id_colaborador'];
                error_log('ID do colaborador encontrado: ' . $dados['id_colaborador']);
            } else {
                error_log('Nenhum ID de colaborador encontrado para o usuário: ' . $dados['id_utilizador']);
            }
            
            error_log('Dados a serem enviados para a DAL: ' . print_r($dados, true));
            
            // Chamar a DAL para atualizar
            $resultado = $this->utilizadorDAL->atualizar($dados);
            error_log('Resultado da atualização: ' . ($resultado ? 'Sucesso' : 'Falha'));
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log('Erro ao atualizar usuário: ' . $e->getMessage());
            throw $e;
        }
    }
}
