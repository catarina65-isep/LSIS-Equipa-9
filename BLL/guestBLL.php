<?php
class GuestBLL {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function registerGuest($data) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Insert into utilizador table
            $query = "INSERT INTO utilizador (
                username,
                email,
                password_hash,
                id_perfil_acesso,
                ativo
            ) VALUES (?, ?, ?, ?, ?)";

            $password = bin2hex(random_bytes(8));
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $params = [
                $data['email'],
                $data['email'],
                $password_hash,
                5, // Guest profile ID
                1
            ];

            $id_utilizador = $this->db->insert($query, $params);

            // Insert into convidado table
            $query = "INSERT INTO convidado (
                id_utilizador,
                nome,
                data_nascimento,
                nif,
                sexo,
                situacao_irs,
                irs_jovem,
                niss,
                cc,
                nacionalidade,
                numero_dependentes,
                morada,
                localidade,
                codigo_postal,
                telemovel,
                iban,
                email,
                matricula,
                nome_emergencia,
                contacto_emergencia,
                grau_parentesco,
                ativo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $id_utilizador,
                $data['nome'],
                $data['dataNascimento'],
                $data['nif'],
                $data['sexo'],
                $data['situacaoIrs'],
                $data['irsJovem'],
                $data['niss'],
                $data['cc'],
                $data['nacionalidade'],
                $data['numeroDependentes'],
                $data['morada'],
                $data['localidade'],
                $data['codigoPostal'],
                $data['telemovel'],
                $data['iban'],
                $data['email'],
                $data['matricula'],
                $data['nomeEmergencia'],
                $data['contactoEmergencia'],
                $data['grauParentesco'],
                1
            ];

            $id_convidado = $this->db->insert($query, $params);

            // Commit transaction
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Ficha salva com sucesso!',
                'id_convidado' => $id_convidado
            ];

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            throw $e;
        }
    }
}