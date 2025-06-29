<?php
require_once '../config/config.php';

// Função para processar upload de documento
function processDocumentUpload() {
    global $conn;
    
    // Verificar se é um upload de documento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
        $userId = $_POST['usuario_id'];
        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];
        $data_validade = isset($_POST['data_validade']) ? $_POST['data_validade'] : null;
        
        // Configurar diretório de upload
        $uploadDir = '../uploads/documentos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Processar arquivo
        $file = $_FILES['arquivo'];
        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFileName;
        
        // Validar tipo de arquivo
        $validExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        if (!in_array($fileExt, $validExtensions)) {
            return array(
                'success' => false,
                'message' => 'Formato de arquivo não suportado'
            );
        }
        
        // Validar tamanho do arquivo (máximo 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return array(
                'success' => false,
                'message' => 'O arquivo deve ter no máximo 5MB'
            );
        }
        
        // Mover arquivo para o diretório
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Inserir no banco de dados
            $stmt = $conn->prepare("
                INSERT INTO documentos (usuario_id, nome, tipo, arquivo, data_validade)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("issss", 
                $userId,
                $nome,
                $tipo,
                $newFileName,
                $data_validade
            );
            
            if ($stmt->execute()) {
                return array(
                    'success' => true,
                    'message' => 'Documento adicionado com sucesso'
                );
            } else {
                return array(
                    'success' => false,
                    'message' => 'Erro ao inserir no banco de dados'
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => 'Erro ao mover arquivo'
            );
        }
    }
    
    return array(
        'success' => false,
        'message' => 'Requisição inválida'
    );
}

// Processar requisição
header('Content-Type: application/json');
$result = processDocumentUpload();
echo json_encode($result);
