<?php
session_start();

require_once __DIR__ . '/../BLL/ColaboradorBLL.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verifica se o perfil é de colaborador
if ($_SESSION['id_perfilacesso'] != 4) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

try {
    $colaboradorBLL = new ColaboradorBLL();
    
    // Configurar diretório para uploads
    $uploadDir = __DIR__ . '/../uploads/documentos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Obter os dados do formulário
    $dados = [
        'id_utilizador' => $_SESSION['utilizador_id'],
        'nome' => $_POST['nome'] ?? null,
        'email' => $_POST['email'] ?? null,
        'telefone' => $_POST['telefone'] ?? null,
        'nif' => $_POST['nif'] ?? null,
        'morada' => $_POST['morada'] ?? null,
        'data_nascimento' => $_POST['dataNascimento'] ?? null,
        'genero' => $_POST['genero'] ?? null,
        'estado_civil' => $_POST['estadoCivil'] ?? null,
        'niss' => $_POST['niss'] ?? null,
        'numero_mecanografico' => $_POST['cartaoCidadao'] ?? null,
        'nib' => $_POST['iban'] ?? null,
        'numero_dependentes' => isset($_POST['numeroDependentes']) ? (int)$_POST['numeroDependentes'] : 0,
        'habilitacoes' => $_POST['habilitacoes'] ?? null,
        'contacto_emergencia' => $_POST['contactoEmergencia'] ?? null,
        'relacao_emergencia' => $_POST['relacaoEmergencia'] ?? null,
        'telemovel_emergencia' => $_POST['telemovelEmergencia'] ?? null
    ];

    // Log dos dados recebidos
    error_log("Dados recebidos: " . print_r($dados, true));
    error_log("NIF recebido: " . ($dados['nif'] ?? 'null'));

    // Verificar se todos os campos obrigatórios foram preenchidos
    $erros = [];
    if (!$dados['nome']) $erros[] = 'Nome é obrigatório';
    if (!$dados['email']) $erros[] = 'Email é obrigatório';
    if (!$dados['nif']) $erros[] = 'NIF é obrigatório';
    if (!$dados['morada']) $erros[] = 'Morada é obrigatória';
    if (!$dados['data_nascimento']) $erros[] = 'Data de Nascimento é obrigatória';
    if (!$dados['genero']) $erros[] = 'Género é obrigatório';
    if (!$dados['estado_civil']) $erros[] = 'Estado Civil é obrigatório';
    if (!$dados['niss']) $erros[] = 'NISS é obrigatório';
    if (!$dados['numero_mecanografico']) $erros[] = 'Número do Cartão de Cidadão é obrigatório';
    if (!$dados['nib']) $erros[] = 'IBAN é obrigatório';
    // Número de dependentes é obrigatório, mas aceita zero
    if (!isset($dados['numero_dependentes'])) $erros[] = 'Número de Dependentes é obrigatório';
    if (!$dados['habilitacoes']) $erros[] = 'Habilitações Literárias são obrigatórias';
    if (!$dados['contacto_emergencia']) $erros[] = 'Contacto de Emergência é obrigatório';
    if (!$dados['relacao_emergencia']) $erros[] = 'Relação com o Contacto de Emergência é obrigatória';
    if (!$dados['telemovel_emergencia']) $erros[] = 'Telemóvel do Contacto de Emergência é obrigatório';
    
    // Validar formatos
    if ($dados['nif'] && strlen($dados['nif']) != 9) $erros[] = 'NIF deve ter 9 dígitos';
    if ($dados['telefone'] && !preg_match('/^[0-9]{9}$/', $dados['telefone'])) $erros[] = 'Telefone deve ter 9 dígitos';
    if ($dados['niss'] && !preg_match('/^[0-9]{11}$/', $dados['niss'])) $erros[] = 'NISS deve ter 11 dígitos';
    if ($dados['numero_mecanografico'] && !preg_match('/^[0-9]{8}$/', $dados['numero_mecanografico'])) $erros[] = 'Número do Cartão de Cidadão deve ter 8 dígitos';
    if ($dados['nib'] && !preg_match('/^[A-Z]{2}[0-9]{22}$/', $dados['nib'])) $erros[] = 'IBAN deve seguir o formato correto';
    if ($dados['telemovel_emergencia'] && !preg_match('/^[0-9]{9}$/', $dados['telemovel_emergencia'])) $erros[] = 'Telemóvel deve ter 9 dígitos';
    // Validar número de dependentes
    if (!isset($dados['numero_dependentes'])) {
        $erros[] = 'Número de Dependentes é obrigatório';
    } elseif (!is_numeric($dados['numero_dependentes'])) {
        $erros[] = 'Número de dependentes deve ser um número válido';
    } elseif ($dados['numero_dependentes'] < 0) {
        $erros[] = 'Número de dependentes deve ser um número positivo ou zero';
    }

    // Validar e processar uploads
    $uploadErros = [];
    
    // Verificar se campos com upload precisam de arquivos
    // Primeiro verificar se já existem documentos
    $uploadDir = __DIR__ . '/../uploads/documentos/';
    
    // Verificar Cartão de Cidadão
    if ($dados['numero_mecanografico']) {
        $cartaoCidadaoFiles = glob($uploadDir . 'cartaocidadao_' . $dados['id_utilizador'] . '_*');
        if (empty($cartaoCidadaoFiles) && (!isset($_FILES['cartaoCidadaoDoc']) || $_FILES['cartaoCidadaoDoc']['error'] !== UPLOAD_ERR_OK)) {
            $uploadErros[] = 'O Cartão de Cidadão requer um comprovativo em formato PDF, JPG ou PNG.';
        }
    }
    
    // Verificar IBAN
    if ($dados['nib']) {
        $ibanFiles = glob($uploadDir . 'iban_' . $dados['id_utilizador'] . '_*');
        if (empty($ibanFiles) && (!isset($_FILES['ibanDoc']) || $_FILES['ibanDoc']['error'] !== UPLOAD_ERR_OK)) {
            $uploadErros[] = 'O IBAN requer um comprovativo bancário em formato PDF, JPG ou PNG.';
        }
    }
    
    // Verificar Morada
    if ($dados['morada']) {
        $moradaFiles = glob($uploadDir . 'morada_' . $dados['id_utilizador'] . '_*');
        if (empty($moradaFiles) && (!isset($_FILES['moradaDoc']) || $_FILES['moradaDoc']['error'] !== UPLOAD_ERR_OK)) {
            $uploadErros[] = 'A Morada requer um comprovativo de morada em formato PDF, JPG ou PNG.';
        }
    }
    
    // Processar comprovativo do Cartão de Cidadão
    if (isset($_FILES['cartaoCidadaoDoc']) && $_FILES['cartaoCidadaoDoc']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cartaoCidadaoDoc'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadErros[] = 'Formato inválido para o comprovativo do Cartão de Cidadão. Apenas PDF, JPG e PNG são permitidos.';
        } else {
            $newName = 'cartaocidadao_' . $dados['id_utilizador'] . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $uploadErros[] = 'Erro ao fazer upload do comprovativo do Cartão de Cidadão.';
            }
        }
    }

    // Processar comprovativo do IBAN
    if (isset($_FILES['ibanDoc']) && $_FILES['ibanDoc']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['ibanDoc'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadErros[] = 'Formato inválido para o comprovativo bancário. Apenas PDF, JPG e PNG são permitidos.';
        } else {
            $newName = 'iban_' . $dados['id_utilizador'] . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $uploadErros[] = 'Erro ao fazer upload do comprovativo bancário.';
            }
        }
    }

    // Processar comprovativo de Morada
    if (isset($_FILES['moradaDoc']) && $_FILES['moradaDoc']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['moradaDoc'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadErros[] = 'Formato inválido para o comprovativo de morada. Apenas PDF, JPG e PNG são permitidos.';
        } else {
            $newName = 'morada_' . $dados['id_utilizador'] . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $uploadErros[] = 'Erro ao fazer upload do comprovativo de morada.';
            }
        }
    }

    if (!empty($uploadErros)) {
        $erros = array_merge($erros, $uploadErros);
    }
    
    if (!empty($erros)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, corrija os seguintes erros:',
            'erros' => $erros
        ]);
        exit;
    }

    // Atualizar os dados do colaborador
    $resultado = $colaboradorBLL->atualizar($dados);
    
    if ($resultado) {
        // Buscar os dados atualizados para confirmar
        $colaborador = $colaboradorBLL->buscarPorId($_SESSION['utilizador_id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Dados atualizados com sucesso',
            'dados_atualizados' => $colaborador
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar dados. Por favor, tente novamente.'
        ]);
        exit;
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar os dados: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
    exit;
}
