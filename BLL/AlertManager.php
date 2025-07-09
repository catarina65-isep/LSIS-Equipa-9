<?php
require_once __DIR__ . '/../DAL/database.php';

class AlertManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    /**
     * Get the PDO connection
     * @return PDO
     */
    public function getPdo() {
        return $this->pdo;
    }
    
    /**
     * Schedule the next update reminder for a user
     */
    public function scheduleNextUpdateReminder($userId, $periodMonths) {
        $nextReminder = new DateTime();
        $nextReminder->modify("+{$periodMonths} months");
        
        $stmt = $this->pdo->prepare("
            INSERT INTO user_data_updates (user_id, last_updated, next_reminder, status)
            VALUES (:user_id, NOW(), :next_reminder, 'pending')
            ON DUPLICATE KEY UPDATE 
                last_updated = VALUES(last_updated),
                next_reminder = VALUES(next_reminder),
                status = 'pending',
                updated_at = NOW()
        ");
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':next_reminder' => $nextReminder->format('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get users who need to be reminded to update their data
     */
    public function getUsersNeedingReminder() {
        $stmt = $this->pdo->query("
            SELECT 
                u.id_utilizador as id, 
                COALESCE(c.nome, u.username) as nome, 
                COALESCE(c.email, u.email) as email, 
                udu.next_reminder
            FROM utilizador u
            LEFT JOIN colaborador c ON u.id_utilizador = c.id_utilizador
            JOIN user_data_updates udu ON u.id_utilizador = udu.user_id
            WHERE udu.next_reminder <= NOW() 
            AND udu.status = 'pending'
            AND u.ativo = 1
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mark a reminder as sent
     */
    public function markReminderSent($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE user_data_updates 
            SET status = 'pending', 
                updated_at = NOW()
            WHERE user_id = ?
        ");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Send reminder emails to users who need to update their data
     */
    public function sendReminderEmails() {
        require_once __DIR__ . '/../DAL/PHPMailerConfig.php';
        
        if (!function_exists('sendEmail')) {
            error_log('ERRO: A função sendEmail não foi encontrada');
            return false;
        }
        
        $users = $this->getUsersNeedingReminder();
        $sentCount = 0;
        
        foreach ($users as $user) {
            $subject = "Atualização de Dados Pendente - {$user['nome']}";
            
            $message = "
            <html>
            <head>
                <title>Atualização de Dados Pendente</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { 
                        display: inline-block; 
                        padding: 10px 20px; 
                        background-color: #2c3e50; 
                        color: white; 
                        text-decoration: none; 
                        border-radius: 5px; 
                        margin: 20px 0;
                    }
                    .footer { 
                        margin-top: 30px; 
                        font-size: 12px; 
                        color: #777; 
                        border-top: 1px solid #eee; 
                        padding-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Atualização de Dados Pendente</h2>
                    <p>Olá {$user['nome']},</p>
                    <p>De acordo com nossa política de atualização de dados, é necessário que você atualize suas informações cadastrais.</p>
                    <p>Por favor, acesse o sistema e verifique se todos os seus dados estão corretos.</p>
                    <p><a href=\"http://localhost:8888/LSIS-Equipa-9/UI/perfil.php\" class=\"button\">Atualizar Meus Dados</a></p>
                    <div class='footer'>
                        <p>Este é um e-mail automático. Por favor, não responda a esta mensagem.</p>
                        <p>Atenciosamente,<br>Equipe de Recursos Humanos<br>Tlantic</p>
                    </div>
                </div>
            </body>
            </html>";
            
            if (@sendEmail($user['email'], $subject, $message)) {
                $this->markReminderSent($user['id']);
                $sentCount++;
            }
        }
        
        return $sentCount;
    }
    
    /**
     * Get the current update period in months
     */
    public function getUpdatePeriod() {
        $configFile = __DIR__ . '/../UI/recursos_humanos/alerta_config.json';
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            return $config['periodicidade'] ?? 12; // Default to 12 months
        }
        return 12; // Default to 12 months if config file doesn't exist
    }
    
    /**
     * Update the reminder period for all users
     */
    public function updateAllUsersReminderPeriod($periodMonths) {
        $periodMonths = max(1, (int)$periodMonths);
        
        // Atualizar o arquivo de configuração
        $configFile = __DIR__ . '/../UI/recursos_humanos/alerta_config.json';
        $configDir = dirname($configFile);
        
        // Verificar se o diretório existe e tem permissão de escrita
        if (!is_dir($configDir)) {
            error_log("ERRO: Diretório não existe: $configDir");
            if (!mkdir($configDir, 0755, true)) {
                error_log("ERRO: Falha ao criar diretório: $configDir");
            }
        }
        
        if (!is_writable($configDir)) {
            error_log("ERRO: Sem permissão de escrita no diretório: $configDir");
        }
        
        $configData = json_encode(['periodicidade' => $periodMonths]);
        if (file_put_contents($configFile, $configData) === false) {
            error_log("ERRO: Falha ao escrever no arquivo de configuração: $configFile");
        } else {
            error_log("Configuração salva com sucesso em: $configFile");
        }
        
        // Agendar atualizações para todos os utilizadores ativos
        try {
            $stmt = $this->pdo->query("SELECT id_utilizador FROM utilizador WHERE ativo = 1");
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("Total de utilizadores ativos encontrados: " . count($users));
            
            $updated = 0;
            foreach ($users as $userId) {
                error_log("Atualizando lembrete para o utilizador ID: $userId");
                if ($this->scheduleNextUpdateReminder($userId, $periodMonths)) {
                    $updated++;
                    error_log("Lembrete atualizado com sucesso para o utilizador ID: $userId");
                } else {
                    error_log("Falha ao atualizar lembrete para o utilizador ID: $userId");
                }
            }
            
            error_log("Total de lembretes atualizados: $updated");
            return $updated;
            
        } catch (Exception $e) {
            error_log("ERRO ao atualizar lembretes: " . $e->getMessage());
            return 0;
        }
    }
}
