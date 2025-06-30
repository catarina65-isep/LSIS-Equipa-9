<?php
require_once __DIR__ . '/../DAL/LoginDAL.php';

class LoginBLL {
    private $loginDAL;

    public function __construct() {
        $this->loginDAL = new LoginDAL();
    }

    public function autenticar($email, $senha) {
        try {
            // Validação básica
            if (empty($email) || empty($senha)) {
                throw new Exception('Por favor, preencha todos os campos.');
            }

            // Verifica se o email é válido
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Por favor, insira um email válido.');
            }
            
            // Verifica se o email está no formato correto (algo@perfil.com ou algo@tlantic.pt)
            if (!preg_match('/^[^@]+@(administrador|recursoshumanos|rh|coordenador|colaborador|tlantic\.pt)(\.[^.]*)?$/', $email)) {
                throw new Exception('O email deve estar no formato: usuario@perfil.com ou usuario@tlantic.pt, onde perfil pode ser administrador, recursoshumanos, coordenador ou colaborador');
            }

            // Obtém o perfil com base no domínio do email
            $idPerfil = $this->loginDAL->obterPerfilPorEmail($email);
            
            if ($idPerfil === null) {
                throw new Exception('Domínio de email não reconhecido.');
            }

            // Verifica as credenciais no banco de dados
            $usuario = $this->loginDAL->verificarCredenciais($email, $senha);
            
            if ($usuario === false) {
                throw new Exception('Email ou senha incorretos.');
            }

            // Verifica se o perfil do usuário corresponde ao domínio do email
            if ($usuario['id_perfilacesso'] != $idPerfil) {
                throw new Exception('Acesso não autorizado para este perfil.');
            }

            // Retorna os dados do usuário sem a senha
            unset($usuario['senha']);
            return $usuario;

        } catch (Exception $e) {
            error_log('Erro na autenticação: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica se o usuário está autenticado e tem permissão de administrador
     * @return bool Retorna true se o usuário estiver autenticado e for administrador, caso contrário, redireciona para a página de login
     */
    public function verificarAutenticacao() {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_perfilacesso'])) {
            return false;
        }
        
        // Verifica se o perfil é de administrador (id_perfilacesso = 1)
        if ($_SESSION['id_perfilacesso'] != 1) {
            return false;
        }
        
        return true;
    }
}
?>
