<?php
require_once __DIR__ . '/../DAL/PerfilAcessoDAL.php';

class PerfilAcessoBLL {
    private $perfilAcessoDAL;

    public function __construct() {
        $this->perfilAcessoDAL = new PerfilAcessoDAL();
    }

    /**
     * Lista todos os perfis de acesso ativos
     * @return array Lista de perfis de acesso
     */
    public function listarTodos() {
        try {
            return $this->perfilAcessoDAL->listarTodos();
        } catch (Exception $e) {
            error_log('Erro ao listar perfis de acesso: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém um perfil de acesso pelo ID
     * @param int $id ID do perfil de acesso
     * @return array|null Dados do perfil de acesso ou null se não encontrado
     */
    public function obterPorId($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID de perfil de acesso inválido');
            }
            
            return $this->perfilAcessoDAL->obterPorId($id);
        } catch (Exception $e) {
            error_log('Erro ao obter perfil de acesso: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Exclui um perfil de acesso pelo ID
     * @param int $id ID do perfil de acesso a ser excluído
     * @return array Resultado da operação com status e mensagem
     */
    /**
     * Exclui um perfil de acesso pelo ID
     * @param int $id ID do perfil de acesso a ser excluído
     * @return array Resultado da operação com status e mensagem
     */
    public function excluir($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID de perfil de acesso inválido');
            }
            
            // Verifica se o perfil existe
            $perfil = $this->perfilAcessoDAL->obterPorId($id);
            if (!$perfil) {
                throw new Exception('Perfil não encontrado');
            }
            
            // Verifica se é um perfil do sistema que não pode ser excluído
            if ($id <= 4) { // IDs 1 a 4 são perfis do sistema
                throw new Exception('Não é permitido excluir perfis do sistema');
            }
            
            // Tenta excluir o perfil
            $resultado = $this->perfilAcessoDAL->excluir($id);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Perfil excluído com sucesso.'
                ];
            } else {
                throw new Exception('Não foi possível excluir o perfil.');
            }
            
        } catch (Exception $e) {
            error_log('Erro ao excluir perfil de acesso: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
