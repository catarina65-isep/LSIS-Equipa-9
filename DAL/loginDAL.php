<?php
require_once __DIR__ . '/database.php';

class LoginDAL {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function verificarCredenciais($email, $senha) {
        try {
            // Verifica se existem usuários na tabela
            $query = "SELECT COUNT(*) as total FROM utilizador";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                // Se existirem usuários, faz a verificação normal
                $query = "SELECT u.*, p.nome as perfil 
                         FROM utilizador u 
                         INNER JOIN perfilacesso p ON u.id_perfilacesso = p.id_perfilacesso 
                         WHERE u.email = :email AND u.senha = :senha AND u.ativo = 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', md5($senha));
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }
                
                return false;
            } else {
                // Se não existirem usuários, cria um usuário temporário com base no email
                $perfil = $this->obterPerfilPorEmail($email);
                
                if ($perfil === null) {
                    return false;
                }
                
                // Retorna um array simulado com os dados do usuário
                return [
                    'id_utilizador' => 1,
                    'email' => $email,
                    'nome' => 'Usuário de Teste',
                    'id_perfilacesso' => $perfil,
                    'perfil' => $this->obterNomePerfil($perfil)
                ];
            }
            
        } catch(PDOException $e) {
            error_log('Erro ao verificar credenciais: ' . $e->getMessage());
            // Em caso de erro, permite o acesso de qualquer forma para desenvolvimento
            $perfil = $this->obterPerfilPorEmail($email);
            
            if ($perfil === null) {
                return false;
            }
            
            return [
                'id_utilizador' => 1,
                'email' => $email,
                'nome' => 'Usuário de Teste',
                'id_perfilacesso' => $perfil,
                'perfil' => $this->obterNomePerfil($perfil)
            ];
        }
    }

    private function obterNomePerfil($idPerfil) {
        $perfis = [
            1 => 'Administrador',
            2 => 'Recursos Humanos',
            3 => 'Coordenador',
            4 => 'Colaborador'
        ];
        
        return $perfis[$idPerfil] ?? 'Desconhecido';
    }
    
    public function obterPerfilPorEmail($email) {
        try {
            // Carrega a configuração de perfis
            $perfisConfig = require __DIR__ . '/../config/perfis.php';
            
            // Extrai o domínio do email (parte após o @)
            $partes = explode('@', $email);
            if (count($partes) !== 2) {
                return null;
            }
            
            $dominio = strtolower($partes[1]); // Pega a parte após o @
            
            // Mapeia domínios para perfis
            $mapeamentoPerfis = [
                'administrador' => 1,
                'recursoshumanos' => 2,
                'rh' => 2, // Alternativa para recursos humanos
                'coordenador' => 3,
                'colaborador' => 4,
                'tlantic' => 1 // Domínio tlanic.pt mapeado para administrador
            ];
            
            // Verifica se o domínio está mapeado
            foreach ($mapeamentoPerfis as $chave => $valor) {
                if (strpos($dominio, $chave) !== false) {
                    return $valor;
                }
            }
            
            return null; // Retorna null se não encontrar um perfil correspondente
            
        } catch (Exception $e) {
            error_log('Erro ao obter perfil por email: ' . $e->getMessage());
            throw new Exception('Erro ao identificar perfil do usuário.');
        }
    }
}
?>
