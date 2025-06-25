<?php
class Auth {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilizador WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Get user role
                $stmt = $this->db->prepare("SELECT descricao FROM perfilacesso WHERE id_perfil_acesso = ?");
                $stmt->execute([$user['id_perfil_acesso']]);
                $role = $stmt->fetch(PDO::FETCH_ASSOC)['descricao'];

                // Start session and set user data
                $_SESSION['user_id'] = $user['id_utilizador'];
                $_SESSION['user_role'] = $role;
                $_SESSION['username'] = $user['username'];
                
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function register($username, $email, $password, $perfil_acesso) {
        try {
            $stmt = $this->db->prepare("INSERT INTO utilizador (username, email, password_hash, id_perfil_acesso) VALUES (?, ?, ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$username, $email, $hashedPassword, $perfil_acesso]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserRole() {
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    }
}
?>
