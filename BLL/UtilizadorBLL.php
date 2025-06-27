<?php
require_once __DIR__ . '/../DAL/UtilizadorDAL.php';

class UtilizadorBLL {
    private $utilizadorDAL;

    public function __construct() {
        $this->utilizadorDAL = new UtilizadorDAL();
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
     * Lista todos os usuários ativos
     * @return array Lista de usuários ativos
     */
    public function listarUtilizadoresAtivos() {
        try {
            $todosUsuarios = $this->utilizadorDAL->listarTodos();
            return array_filter($todosUsuarios, function($usuario) {
                return $usuario['ativo'] == 1; // Filtra apenas usuários ativos
            });
        } catch (Exception $e) {
            error_log('Erro ao listar usuários ativos: ' . $e->getMessage());
            return [];
        }
    }
}
