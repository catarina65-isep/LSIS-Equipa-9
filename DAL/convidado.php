<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utilidades.php';

class Convidado {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function criarConvidado($dados) {
        try {
            // Iniciar transação
            $this->conn->beginTransaction();

            // Preparar a query
            $query = "INSERT INTO convidado (
                nome_completo,
                data_nascimento,
                nif,
                sexo,
                situacao_irs,
                irs_jovem,
                niss,
                cc,
                nacionalidade,
                dependentes,
                empresa,
                cartao_cidadao,
                motivo,
                morada_residencia,
                localidade,
                codigo_postal,
                comprovativo_morada,
                contacto_telefonico,
                telemovel,
                iban,
                email,
                nome_emergencia,
                telefone_emergencia,
                parentesco_emergencia,
                validade_convite,
                responsavel,
                data_inicio,
                data_fim,
                ativo,
                aceite_termos,
                matricula,
                data_criacao,
                data_atualizacao
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?
            )";

            $stmt = $this->conn->prepare($query);

            // Validação de datas
            $dados['data_nascimento'] = date('Y-m-d', strtotime($dados['data_nascimento']));
            $dados['validade_convite'] = isset($dados['validade_convite']) ? date('Y-m-d', strtotime($dados['validade_convite'])) : null;

            // Upload de arquivos
            $cartao_cidadao = $this->uploadDocumento('cartaocidadao', 'cartoes_cidadao');
            $comprovativo_morada = $this->uploadDocumento('comprovativomorada', 'comprovativos_morada');

            // Bind dos parâmetros usando PDO
            $stmt->bindValue(1, $dados['nome_completo']);  // nome_completo
            $stmt->bindValue(2, $dados['data_nascimento']);  // data_nascimento
            $stmt->bindValue(3, $dados['nif']);  // nif
            $stmt->bindValue(4, $dados['sexo']);  // sexo
            $stmt->bindValue(5, $dados['situacao_irs']);  // situacao_irs
            $stmt->bindValue(6, $dados['irs_jovem']);  // irs_jovem
            $stmt->bindValue(7, $dados['niss']);  // niss
            $stmt->bindValue(8, $dados['cc']);  // cc
            $stmt->bindValue(9, $dados['nacionalidade']);  // nacionalidade
            $stmt->bindValue(10, $dados['dependentes']);  // dependentes
            $stmt->bindValue(11, $dados['empresa']);  // empresa
            $stmt->bindValue(12, $cartao_cidadao);  // cartao_cidadao
            $stmt->bindValue(13, $dados['motivo']);  // motivo
            $stmt->bindValue(14, $dados['morada_residencia']);  // morada_residencia
            $stmt->bindValue(15, $dados['localidade']);  // localidade
            $stmt->bindValue(16, $dados['codigo_postal']);  // codigo_postal
            $stmt->bindValue(17, $comprovativo_morada);  // comprovativo_morada
            $stmt->bindValue(18, $dados['contacto_telefonico']);  // contacto_telefonico
            $stmt->bindValue(19, $dados['telemovel']);  // telemovel
            $stmt->bindValue(20, $dados['iban']);  // iban
            $stmt->bindValue(21, $dados['email']);  // email
            $stmt->bindValue(22, $dados['nome_emergencia']);  // nome_emergencia
            $stmt->bindValue(23, $dados['telefone_emergencia']);  // telefone_emergencia
            $stmt->bindValue(24, $dados['parentesco_emergencia']);  // parentesco_emergencia
            $stmt->bindValue(25, $dados['validade_convite']);  // validade_convite
            $stmt->bindValue(26, null, PDO::PARAM_NULL);  // responsavel
            $stmt->bindValue(27, $dados['data_inicio']);  // data_inicio
            $stmt->bindValue(28, null, PDO::PARAM_NULL);  // data_fim
            $stmt->bindValue(29, $dados['ativo'], PDO::PARAM_INT);  // ativo
            $stmt->bindValue(30, $dados['aceite_termos'], PDO::PARAM_INT);  // aceite_termos
            $stmt->bindValue(31, $dados['matricula']);  // matricula
            $stmt->bindValue(32, 'CURRENT_TIMESTAMP', PDO::PARAM_STR);  // data_criacao
            $stmt->bindValue(33, 'CURRENT_TIMESTAMP', PDO::PARAM_STR);  // data_atualizacao

            // Executar query
            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                throw new Exception("Erro ao criar convidado: " . $stmt->error);
            }

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    private function uploadDocumento($campo, $diretorio) {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] != UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = "../uploads/$diretorio/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
        $novoNome = uniqid() . "_" . time() . "_" . $_FILES[$campo]['name'];
        $caminhoDestino = $uploadDir . $novoNome;

        if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminhoDestino)) {
            return $novoNome;
        }
        return null;
    }

    public function validarDados($dados) {
        $erros = [];

        // Campos obrigatórios
        $camposObrigatorios = [
            'nome_completo', 'data_nascimento', 'nif', 'sexo',
            'morada_residencia', 'localidade', 'codigo_postal',
            'contacto_telefonico', 'email', 'aceite_termos'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                $erros[] = "Campo $campo é obrigatório";
            }
        }

        // Validar telefone
        if (!empty($dados['contacto_telefonico'])) {
            $telefone = validarTelefone($dados['contacto_telefonico']);
            if ($telefone === null) {
                $erros[] = "Número de telefone inválido (deve ter 9 dígitos)";
            } else {
                $dados['contacto_telefonico'] = $telefone;
            }
        }

        // Validação específica
        if (isset($dados['nif']) && !preg_match("/^[0-9]{9}$/", $dados['nif'])) {
            $erros[] = "NIF inválido";
        }

        if (isset($dados['contacto_telefonico']) && !preg_match("/^[0-9]{9}$/", $dados['contacto_telefonico'])) {
            $erros[] = "Número de telefone inválido";
        }

        if (isset($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido";
        }

        return $erros;
    }
}
