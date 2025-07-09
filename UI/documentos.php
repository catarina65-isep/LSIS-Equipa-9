<?php
session_start();

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

$utilizador_id = $_GET['id_utilizador'] ?? null;

if (!$utilizador_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID do utilizador não fornecido']);
    exit;
}

// Diretório de uploads
$uploadDir = __DIR__ . '/../uploads/documentos/';

// Tipos de documentos e seus prefixos
$documentos = [
    'morada' => 'Comprovativo de Morada',
    'cartaocidadao' => 'Cartão de Cidadão',
    'nif' => 'NIF',
    'niss' => 'NISS',
    'iban' => 'IBAN'
];

// Inicializar HTML
$html = '<div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Data de Upload</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>';

// Para cada tipo de documento
foreach ($documentos as $prefix => $descricao) {
    $files = glob($uploadDir . $prefix . '_' . $utilizador_id . '_*');
    if (!empty($files)) {
        $latestFile = array_reduce($files, function($a, $b) {
            return filemtime($a) > filemtime($b) ? $a : $b;
        });
        $fileName = basename($latestFile);
        $uploadDate = date('Y-m-d H:i:s', filemtime($latestFile));
        $status = 'Pendente';
        
        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($descricao) . "</td>";
        $html .= "<td>" . htmlspecialchars($fileName) . "</td>";
        $html .= "<td>" . htmlspecialchars($uploadDate) . "</td>";
        $html .= "<td class='text-warning'>" . htmlspecialchars($status) . "</td>";
        $html .= "<td>";
        $html .= "<a href='" . htmlspecialchars("../uploads/documentos/" . $fileName) . "' target='_blank' class='btn btn-primary btn-sm'>";
        $html .= "<i class='bx bx-download'></i> Download";
        $html .= "</a>";
        $html .= "<button type='button' class='btn btn-danger btn-sm ms-2 btn-apagar-documento' ";
        $html .= "data-file='" . htmlspecialchars($fileName) . "' ";
        $html .= "data-field='" . htmlspecialchars($prefix) . "' ";
        $html .= "onclick='apagarDocumento(event)'>";
        $html .= "<i class='bx bx-trash'></i>";
        $html .= "</button>";
        $html .= "</td>";
        $html .= "</tr>";
    }
}

$html .= '</tbody>
        </table>
    </div>';

header('Content-Type: text/html');
echo $html;
