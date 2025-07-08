<?php
session_start();
require_once __DIR__ . '/../DAL/config.php';
require_once __DIR__ . '/../DAL/colaboradorDAL.php';

// Inicializar a conexão com o banco de dados
try {
    $colaboradorDAL = new ColaboradorDAL(Database::getInstance());
    $colaborador = $colaboradorDAL->buscarPorId($_SESSION['utilizador_id']);

    // Configurar cabeçalhos para download
    header('Content-Type: application/vnd.ms-excel; charset=ISO-8859-1');
    header('Content-Disposition: attachment;filename="dados_colaborador.csv"');
    header('Cache-Control: max-age=0');

    // Criar o CSV
    $output = fopen('php://output', 'w');

    // Escrever os dados
    $data = [
        ['Dados do Colaborador', ''],
        ['Informação atualizada em: ' . date('d/m/Y H:i'), ''],
        [''],
        ['Informações Pessoais', ''],
        ['Nome', "'" . $colaborador['nome']],
        ['Email', "'" . $colaborador['email']],
        ['Telefone', "'" . $colaborador['telefone']],
        ['NIF', "'" . $colaborador['nif']],
        ['Morada', "'" . $colaborador['morada']],
        ['Data de Nascimento', "'" . date('d/m/Y', strtotime($colaborador['data_nascimento']))],
        ['Género', "'" . $colaborador['genero']],
        ['Estado Civil', "'" . $colaborador['estado_civil']],
        ['NISS', "'" . $colaborador['niss']],
        ['Número de Dependentes', "'" . $colaborador['numero_dependentes']],
        ['Habilitações Literárias', "'" . $colaborador['habilitacoes']],
        [''],
        ['Informações de Emergência', ''],
        ['Contacto de Emergência', "'" . $colaborador['contacto_emergencia']],
        ['Relação com o Contacto de Emergência', "'" . $colaborador['relacao_emergencia']],
        ['Telemóvel do Contacto de Emergência', "'" . $colaborador['telemovel_emergencia']]
    ];

    // Adicionar uma linha vazia para melhorar a legibilidade
    $data[] = [''];

    // Adicionar uma linha com cabeçalhos mais largos
    $data[] = ['Título', 'Valor'];

    // Adicionar uma linha vazia para separar os cabeçalhos dos dados
    $data[] = [''];

    // Escrever cada linha
    foreach ($data as $row) {
        // Codificar caracteres especiais para ISO-8859-1
        $row = array_map(function($value) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value);
        }, $row);
        fputcsv($output, $row, ';', '"');
    }

    // Fechar o arquivo
    fclose($output);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
