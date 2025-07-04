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
            
            // Obtém o perfil com base no domínio do email
            $idPerfil = $this->loginDAL->obterPerfilPorEmail($email);
            
            if ($idPerfil === null) {
                throw new Exception('Domínio de email não reconhecido.');
            }

            // Verifica as credenciais no banco de dados
            $usuario = $this->loginDAL->verificarCredenciais($email, $senha);
            
            if ($usuario === false) {
                throw new Exception('Credenciais inválidas.');
            }

            // Verifica se o perfil do usuário corresponde ao domínio do email
            if ($usuario['id_perfil_acesso'] != $idPerfil) {
                // Se não corresponder, tenta atualizar o perfil do usuário
                if ($this->atualizarPerfilUsuario($usuario['id_utilizador'], $idPerfil)) {
                    $usuario['id_perfil_acesso'] = $idPerfil;
                    $usuario['perfil'] = $this->loginDAL->obterNomePerfil($idPerfil);
                } else {
                    throw new Exception('Acesso não autorizado para este perfil.');
                }
            }

            // Remove dados sensíveis antes de retornar
            unset($usuario['password_hash']);
            unset($usuario['token_recuperacao']);
            unset($usuario['token_expiracao']);
            
            return $usuario;

        } catch (PDOException $e) {
            error_log('Erro na autenticação: ' . $e->getMessage());
            throw new Exception('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.');
        } catch (Exception $e) {
            // Relança a exceção para ser tratada pelo chamador
            throw $e;
        }
    }
    
    /**
     * Atualiza o perfil de um usuário no banco de dados
     */
    private function atualizarPerfilUsuario($idUsuario, $idPerfil) {
        try {
            $query = "UPDATE utilizador SET id_perfil_acesso = :perfil WHERE id_utilizador = :id";
            $stmt = $this->loginDAL->getConnection()->prepare($query);
            $stmt->bindParam(':perfil', $idPerfil, PDO::PARAM_INT);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erro ao atualizar perfil do usuário: ' . $e->getMessage());
            return false;
        }
    }
}
?>
