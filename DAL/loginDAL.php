<?php
require_once __DIR__ . '/database.php';

class LoginDAL {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function verificarCredenciais($email, $senha) {
        try {
            // Busca o usuário pelo email
            $query = "SELECT u.*, p.nome as perfil 
                     FROM utilizador u 
                     INNER JOIN perfilacesso p ON u.id_perfil_acesso = p.id_perfilacesso 
                     WHERE u.email = :email AND u.ativo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica se encontrou o usuário e se a senha está correta
            if ($usuario && password_verify($senha, $usuario['password_hash'])) {
                // Atualiza o último login
                $this->atualizarUltimoLogin($usuario['id_utilizador']);
                return $usuario;
            }
            
            // Se não encontrou o usuário, verifica se é um usuário de teste
            $perfil = $this->obterPerfilPorEmail($email);
            
            if ($perfil === null) {
                return false;
            }
            
            // Para ambiente de desenvolvimento, permite login com qualquer senha
            // APENAS PARA TESTES - REMOVER EM PRODUÇÃO
            if ($this->isAmbienteDesenvolvimento()) {
                return [
                    'id_utilizador' => 1,
                    'email' => $email,
                    'username' => 'Usuário de Teste',
                    'id_perfil_acesso' => $perfil,
                    'perfil' => $this->obterNomePerfil($perfil)
                ];
            }
            
            return false;
            
        } catch(PDOException $e) {
            error_log('Erro ao verificar credenciais: ' . $e->getMessage());
            
            // Em caso de erro, permite o acesso apenas em ambiente de desenvolvimento
            if ($this->isAmbienteDesenvolvimento()) {
                $perfil = $this->obterPerfilPorEmail($email);
                
                if ($perfil === null) {
                    return false;
                }
                
                return [
                    'id_utilizador' => 1,
                    'email' => $email,
                    'username' => 'Usuário de Teste',
                    'id_perfil_acesso' => $perfil,
                    'perfil' => $this->obterNomePerfil($perfil)
                ];
            }
            
            return false;
        }
    }

    public function obterNomePerfil($idPerfil) {
        $perfis = [
            1 => 'Administrador',
            2 => 'Recursos Humanos',
            3 => 'Coordenador',
            4 => 'Colaborador'
        ];
        
        return $perfis[$idPerfil] ?? 'Desconhecido';
    }
    
    /**
     * Atualiza a data e IP do último login do usuário
     */
    private function atualizarUltimoLogin($idUsuario) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'];
            $dataAtual = date('Y-m-d H:i:s');
            
            $query = "UPDATE utilizador SET 
                     ultimo_login = :data_login,
                     ip_ultimo_login = :ip
                     WHERE id_utilizador = :id";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':data_login', $dataAtual);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':id', $idUsuario);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar último login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    private function isAmbienteDesenvolvimento() {
        // Verifica se é localhost ou 127.0.0.1
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        
        return in_array($serverName, ['localhost', '127.0.0.1']) || 
               strpos($serverAddr, '127.') === 0 || 
               strpos($serverAddr, '192.168.') === 0;
    }
    
    public function obterPerfilPorEmail($email) {
        // Retorna o perfil baseado no email 
        // 1 - Admin
        // 2 - RH
        // 3 - Coordenador
        // 4 - Colaborador (padrão)
        
        // Converte o email para minúsculas para facilitar a comparação
        $email = strtolower(trim($email));
        
        // Verifica se é admin
        if (strpos($email, 'admin@') === 0) {
            return 1; // Admin
        }
        
        // Verifica se é RH
        if (strpos($email, 'rh@') === 0) {
            return 2; // RH
        }
        
        // Verifica se é coordenador
        if (strpos($email, 'coordenador@') === 0) {
            return 3; // Coordenador
        }
        
        // Por padrão, retorna Colaborador
        return 4; // Colaborador
    }
    
    /**
     * Retorna a conexão com o banco de dados
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Registra uma atividade no histórico
     */
    public function registrarAtividade($idUtilizador, $acao, $modulo = 'Sistema', $idRegistro = null, $dadosAdicionais = null) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
            $dados = $dadosAdicionais ? json_encode($dadosAdicionais, JSON_UNESCAPED_UNICODE) : null;
            
            $query = "INSERT INTO historico_acesso 
                     (id_utilizador, acao, modulo, id_registro, ip, user_agent, dados) 
                     VALUES (:id_utilizador, :acao, :modulo, :id_registro, :ip, :user_agent, :dados)";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_utilizador', $idUtilizador, PDO::PARAM_INT);
            $stmt->bindParam(':acao', $acao);
            $stmt->bindParam(':modulo', $modulo);
            $stmt->bindParam(':id_registro', $idRegistro, PDO::PARAM_INT);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->bindParam(':dados', $dados);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao registrar atividade: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtém o histórico de atividades
     */
    public function obterHistoricoAtividades($limite = 100) {
        try {
            $query = "SELECT 
                        h.*, 
                        COALESCE(u.username, 'Usuário Removido') as username, 
                        COALESCE(u.email, CONCAT('user_', h.id_utilizador, '@exemplo.com')) as email,
                        p.nome as perfil
                     FROM historico_acesso h
                     LEFT JOIN utilizador u ON h.id_utilizador = u.id_utilizador
                     LEFT JOIN perfilacesso p ON u.id_perfil_acesso = p.id_perfilacesso
                     ORDER BY h.data_acesso DESC
                     LIMIT :limite";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                return [];
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter histórico de atividades: " . $e->getMessage());
            return [];
        }
    }
}
?>
