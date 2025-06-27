<?php
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../includes/verificar_acesso.php';
require_once __DIR__ . '/../../DAL/Database.php';

$equipaBLL = new EquipaBLL();
$mensagem = '';
$tipoMensagem = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'criar':
                    $equipaId = $equipaBLL->criarEquipa([
                        'nome' => $_POST['nome'],
                        'descricao' => $_POST['descricao'],
                        'coordenador_id' => $_POST['coordenador_id']
                    ]);
                    $mensagem = 'Equipa criada com sucesso!';
                    $tipoMensagem = 'success';
                    // Adicionar membros selecionados
                    if (!empty($_POST['membros']) && is_array($_POST['membros'])) {
                        foreach ($_POST['membros'] as $membroId) {
                            if ($membroId != $_POST['coordenador_id']) {
                                $equipaBLL->adicionarMembro($equipaId, $membroId);
                            }
                        }
                    }
                    break;
                    
                case 'atualizar':
                    $equipaBLL->atualizarEquipa($_POST['id'], [
                        'nome' => $_POST['nome'],
                        'descricao' => $_POST['descricao'],
                        'coordenador_id' => $_POST['coordenador_id']
                    ]);
                    
                    // Atualizar membros
                    if (!empty($_POST['membros']) && is_array($_POST['membros'])) {
                        // Primeiro, remove todos os membros
                        // Depois, adiciona os novos membros
                        // (implementação simplificada - em produção, seria melhor fazer uma comparação para evitar remoções/adições desnecessárias)
                        $equipaBLL->removerMembro($_POST['id'], $_POST['coordenador_id']); // Remove temporariamente o coordenador
                        
                        foreach ($_POST['membros'] as $membroId) {
                            $equipaBLL->adicionarMembro($_POST['id'], $membroId);
                        }
                        
                        // Garante que o coordenador está na equipe
                        $equipaBLL->adicionarMembro($_POST['id'], $_POST['coordenador_id']);
                    }
                    
                    $mensagem = 'Equipa atualizada com sucesso!';
                    $tipoMensagem = 'success';
                    break;
                    
                case 'excluir':
                    $equipaBLL->excluirEquipa($_POST['id']);
                    $mensagem = 'Equipa excluída com sucesso!';
                    $tipoMensagem = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipoMensagem = 'danger';
    }
}

// Obter lista de equipes
$equipas = $equipaBLL->listarEquipas();

// Obter lista de usuários para os selects
$utilizadorBLL = new UtilizadorBLL();
try {
    // Obter a estrutura da tabela para depuração
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SHOW COLUMNS FROM utilizador");
    $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log('Colunas da tabela utilizador: ' . print_r($colunas, true));
    
    // Verificar se existem dados na tabela
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizador");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    error_log('Total de usuários na tabela: ' . $total);
    
    // Listar os primeiros 5 usuários para depuração
    if ($total > 0) {
        $stmt = $pdo->query("SELECT * FROM utilizador LIMIT 5");
        $primeirosUsuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('Primeiros 5 usuários: ' . print_r($primeirosUsuarios, true));
    }
    
    // Obter usuários
    $utilizadores = $utilizadorBLL->listarUtilizadoresAtivos();
    error_log('Número de usuários ativos: ' . count($utilizadores));
    
    // Verificar os primeiros usuários para depuração
    if (!empty($utilizadores)) {
        $primeiroUsuario = reset($utilizadores);
        error_log('Primeiro usuário: ' . print_r($primeiroUsuario, true));
    }
    
} catch (Exception $e) {
    $utilizadores = [];
    $mensagem = 'Erro ao carregar usuários: ' . $e->getMessage();
    $tipoMensagem = 'danger';
    error_log('Erro ao carregar usuários: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
}

// Depuração - remover em produção
echo '<!-- ';
var_dump($equipas);
var_dump($utilizadores);
echo ' -->';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestão de Equipas - Painel de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Estilo de fallback para ícones */
        .bx {
            display: inline-block !important;
            font-size: 1.25em !important;
            width: 1em !important;
            height: 1em !important;
            vertical-align: middle !important;
            background-color: currentColor !important;
            -webkit-mask-size: cover !important;
            mask-size: cover !important;
            -webkit-mask-position: center !important;
            mask-position: center !important;
            -webkit-mask-repeat: no-repeat !important;
            mask-repeat: no-repeat !important;
        }
        .bx-edit-alt {
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M16.293 2.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-13 13A1 1 0 018 21h-4a1 1 0 01-1-1v-4a1 1 0 01.293-.707l10-10 3 3 4-4z'/%3E%3C/svg%3E") no-repeat 50% 50%;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M16.293 2.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-13 13A1 1 0 018 21h-4a1 1 0 01-1-1v-4a1 1 0 01.293-.707l10-10 3 3 4-4z'/%3E%3C/svg%3E") no-repeat 50% 50%;
        }
        .bx-trash {
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M5 20a2 2 0 002 2h10a2 2 0 002-2V8h2V6h-4V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2H3v2h2v12a2 2 0 002 2zm2-16h6v2H7V4z'/%3E%3C/svg%3E") no-repeat 50% 50%;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M5 20a2 2 0 002 2h10a2 2 0 002-2V8h2V6h-4V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2H3v2h2v12a2 2 0 002 2zm2-16h6v2H7V4z'/%3E%3C/svg%3E") no-repeat 50% 50%;
        }
        .bx-plus {
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19 11h-6V5a1 1 0 00-2 0v6H5a1 1 0 000 2h6v6a1 1 0 002 0v-6h6a1 1 0 000-2z'/%3E%3C/svg%3E") no-repeat 50% 50%;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19 11h-6V5a1 1 0 00-2 0v6H5a1 1 0 000 2h6v6a1 1 0 002 0v-6h6a1 1 0 000-2z'/%3E%3C/svg%3E") no-repeat 50% 50%;
        }
    </style>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        /* Page Header */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            color: #4361ee;
            margin-right: 0.5rem;
        }
        
        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn i {
            font-size: 1.1em;
        }
        
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }
        
        .btn-outline-primary {
            color: #4361ee;
            border-color: #4361ee;
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: #4361ee;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }
        
        .btn-outline-danger {
            color: #f72585;
            border-color: #f72585;
            background-color: transparent;
        }
        
        .btn-outline-danger:hover {
            background-color: #f72585;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(247, 37, 133, 0.15);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            padding-left: 250px;
        }
        
        /* Estilos do sidebar movidos para o arquivo sidebar.php */
        
        .main-content {
            width: 100% !important;
            padding: 1.5rem !important;
            min-height: 100vh !important;
            transition: all 0.3s !important;
            background-color: #f5f7fb !important;
            position: relative;
            z-index: 1;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .equipa-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .equipa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: rgba(67, 97, 238, 0.2);
        }
        
        .equipa-card .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        
        .equipa-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #4361ee, #3a56d4);
        }
        
        .equipa-card .card-header h5 {
            margin: 0 0 0 10px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .equipa-card .card-header h5 i {
            margin-right: 8px;
            color: #4361ee;
            font-size: 1.2em;
        }
        
        .equipa-card .card-body {
            padding: 20px 22px;
            flex: 1;
        }
        
        .equipa-card .card-text {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .equipa-card .card-footer {
            background: #f8f9fa;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        
        .equipa-card .btn-group .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        .equipa-card .btn-details {
            flex: 1;
            text-align: center;
            background-color: rgba(67, 97, 238, 0.1);
            color: #4361ee;
            border: 1px solid rgba(67, 97, 238, 0.2);
        }
        
        .equipa-card .btn-details:hover {
            background-color: #4361ee;
            color: #fff;
        }
        
        .equipa-card .btn-edit {
            color: #4361ee;
            background-color: rgba(67, 97, 238, 0.1);
            border: 1px solid rgba(67, 97, 238, 0.2);
        }
        
        .equipa-card .btn-edit:hover {
            background-color: #4361ee;
            color: #fff;
        }
        
        .equipa-card .btn-delete {
            color: #f72585;
            background-color: rgba(247, 37, 133, 0.1);
            border: 1px solid rgba(247, 37, 133, 0.2);
        }
        
        .equipa-card .btn-delete:hover {
            background-color: #f72585;
            color: #fff;
        }
        
        .equipa-card .card-header .btn-outline-primary, 
        .equipa-card .card-header .btn-outline-danger {
            padding: 0 !important;
            font-size: 1rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 30px !important;
            height: 30px !important;
            border-radius: 6px !important;
            transition: all 0.15s ease !important;
            opacity: 1 !important;
            border-width: 1px !important;
            margin: 0 2px !important;
        }
        
        .equipa-card .card-header .btn-outline-primary:hover, 
        .equipa-card .card-header .btn-outline-danger:hover {
            opacity: 1;
        }
        
        .equipa-card .card-header .btn-outline-primary {
            color: var(--primary-color) !important;
            border: 1px solid rgba(67, 97, 238, 0.5) !important;
            background-color: rgba(67, 97, 238, 0.1) !important;
        }
        
        .equipa-card .card-header .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .equipa-card .card-header .btn-outline-danger {
            color: #dc3545 !important;
            border: 1px solid rgba(220, 53, 69, 0.5) !important;
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        
        .equipa-card .card-header .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn-outline-danger:hover {
            background-color: var(--danger-color) !important;
            color: white !important;
            z-index: 1000 !important;
            border: 2px solid green !important;
            background-color: rgba(0, 255, 0, 0.2) !important;
            padding: 0.5rem 1rem !important;
            margin: 0.25rem !important;
        }
        
        /* Garantir que os ícones sejam visíveis */
        .btn i {
            display: inline-block !important;
            width: 1em !important;
            height: 1em !important;
            background-color: currentColor !important;
            border: 1px solid red !important;
        }
        
        .equipa-card .card-footer {
            background: transparent;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem;
        }
        
        .equipa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .top-bar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        /* Modal */
        .modal-backdrop {
            display: none !important;
        }
        
        .modal {
            background-color: transparent;
            pointer-events: none;
        }
        
        .modal-dialog {
            pointer-events: auto;
        }
        
        .modal-dialog.modal-lg {
            max-width: 800px;
            margin: 1.75rem auto;
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }
        
        .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            min-height: auto;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            width: 100%;
            background-color: #fff;
            position: relative;
        }
        
        .modal-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 2rem;
            position: relative;
        }
        
        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #4361ee, #3a56d4);
            border-top-left-radius: 12px;
        }
        
        .modal-title {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.5rem;
            margin: 0;
            line-height: 1.3;
        }
        
        .modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(67, 97, 238, 0.1);
            color: #4361ee;
            font-size: 1.75rem;
        }
        
        .modal-title i {
            color: #4361ee;
            font-size: 1.4rem;
        }
        
        .modal-body {
            padding: 1.5rem 2rem;
            flex: 1;
            overflow-y: auto;
        }
        
        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-lg {
            padding: 0.875rem 2rem;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            border-color: #e1e5eb;
            color: #4a5568;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #d1d5db;
            color: #374151;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-label.required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e1e5eb;
            color: #6c757d;
            transition: all 0.3s;
            min-width: 50px;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .input-group .form-control:first-child,
        .input-group .form-select:first-child {
            border-top-left-radius: 8px !important;
            border-bottom-left-radius: 8px !important;
        }
        
        .form-control, .form-select, .select2-container .select2-selection {
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
            font-size: 1rem;
            min-height: 52px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .form-control-lg {
            font-size: 1.1rem;
            padding: 0.875rem 1.25rem;
        }
        
        .form-control:focus, .form-select:focus, .select2-container--focus .select2-selection {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }
        
        .form-control::placeholder {
            color: #adb5bd;
            opacity: 1;
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
            line-height: 1.5;
        }
        
        /* Estilos para o Select2 */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single {
            min-height: 52px;
            padding: 0.375rem 1rem;
            border: 1px solid #e1e5eb !important;
            border-radius: 8px !important;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px;
            padding-left: 0;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 0;
            line-height: 1.5;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            right: 10px;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f0f2f5;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 8px;
            margin-top: 5px;
            margin-right: 6px;
            color: #2c3e50;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #6c757d;
            margin-right: 5px;
            border-right: 1px solid #dee2e6;
            padding-right: 5px;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc3545;
            background-color: transparent;
        }
        
        /* Estilos para validação */
        .is-invalid {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.8125rem;
            color: #dc3545;
        }
        
        /* Animações */
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .modal.fade .modal-dialog {
            transform: translateY(-50px);
            transition: transform 0.3s ease-out, -webkit-transform 0.3s ease-out;
        }
        
        .modal.show .modal-dialog {
            transform: none;
        }
        
        .modal.fade .modal-content {
            animation: slideIn 0.3s ease-out 0.1s both;
        }
        
        /* Ajustes para dispositivos móveis */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-content {
                min-height: 90vh;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
            }
            
            .modal-body {
                padding: 1.25rem;
                overflow-y: auto;
            }
            
            .modal-footer {
                padding: 1.25rem;
            }
            
            .btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.9375rem;
            }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar {
                margin-left: -250px;
                transition: all 0.3s;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block !important;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .card-footer {
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .card-footer .btn {
                flex: 1 0 calc(50% - 8px);
                min-width: 0;
            }
            
            .card-footer form {
                flex: 1 0 100%;
                margin-top: 8px;
            }
            
            .card-footer .btn-details {
                order: 1;
            }
            
            .card-footer .btn-edit {
                order: 2;
            }
            
            .card-footer form {
                order: 3;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.25rem;
            }
            
            .btn span {
                display: none;
            }
            
            .btn i {
                margin-right: 0;
            }
            
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.8125rem;
                color: #dc3545;
                overflow-y: auto;
            }
        }
    </style>
</head>
<body class="d-flex">
    <!-- Incluir a barra lateral -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Page Header -->
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class='bx bx-group me-2'></i>Gestão de Equipas
                </h1>
                <p class="text-muted mb-0">Gerencie as equipes da sua organização</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" onclick="abrirModalNovaEquipa()">
                    <i class='bx bx-plus me-2'></i>Nova Equipa
                </button>
            </div>
        </div>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?php echo $tipoMensagem; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endif; ?>


                <!-- Lista de Equipas -->
                <div class="row g-4">
                    <?php 
                echo '<!-- Número de equipes: ' . count($equipas) . ' -->';
                if (empty($equipas)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">Nenhuma equipa cadastrada.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($equipas as $equipa): ?>
                            <?php echo '<!-- Iniciando loop para equipe ID: ' . $equipa['id'] . ' -->' . "\n"; ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card equipa-card">
                                    <div class="card-header">
                                        <h5>
                                            <i class='bx bx-group'></i>
                                            <?php echo htmlspecialchars($equipa['nome']); ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <?php 
                                            $descricao = $equipa['descricao'] ?? 'Nenhuma descrição fornecida.';
                                            echo nl2br(htmlspecialchars($descricao)); 
                                            ?>
                                        </p>
                                        <div class="d-flex align-items-center text-muted mb-2">
                                            <i class='bx bx-user me-2' style="color: #4361ee;"></i>
                                            <span>Coordenador: 
                                                <?php 
                                                $coordenador = $equipa['coordenador'] ?? 'Não definido';
                                                echo htmlspecialchars($coordenador);
                                                ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class='bx bx-group me-2' style="color: #4361ee;"></i>
                                            <span>Membros: <?php echo count($equipa['membros'] ?? []); ?></span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="equipa_detalhes.php?id=<?php echo $equipa['id']; ?>" class="btn btn-sm btn-details">
                                            <i class='bx bx-show me-1'></i> Detalhes
                                        </a>
                                        <button class="btn btn-sm btn-edit" 
                                                onclick="editarEquipa(<?php echo htmlspecialchars(json_encode($equipa)); ?>)"
                                                data-bs-toggle="tooltip" 
                                                title="Editar">
                                            <i class='bx bx-edit-alt'></i>
                                        </button>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta equipa?');">
                                            <input type="hidden" name="acao" value="excluir">
                                            <input type="hidden" name="id" value="<?php echo $equipa['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-delete"
                                                    data-bs-toggle="tooltip" 
                                                    title="Excluir">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

    <!-- Modal Criar/Editar Equipa -->
    <div class="modal fade" id="criarEquipaModal" tabindex="-1" aria-labelledby="criarEquipaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon me-3">
                            <i class='bx bx-group'></i>
                        </div>
                        <div>
                            <h4 class="modal-title mb-0" id="modalTitulo">Nova Equipa</h4>
                            <p class="text-muted mb-0 small">Preencha os dados da nova equipa</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                
                <form id="formEquipa" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="acao" id="formAcao" value="criar">
                    <input type="hidden" name="id" id="equipaId">
                    
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Nome da Equipa -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="nome" class="form-label fw-medium">
                                        Nome da Equipa <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class='bx bx-rename text-primary'></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg" id="nome" name="nome" required 
                                               placeholder="Ex: Equipa de Desenvolvimento Web">
                                    </div>
                                    <div class="form-text">
                                        Escolha um nome descritivo para a equipa
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Descrição -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="descricao" class="form-label fw-medium">
                                        Descrição
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light align-items-start pt-2">
                                            <i class='bx bx-text text-primary'></i>
                                        </span>
                                        <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                                placeholder="Descreva os objetivos e responsabilidades desta equipa"></textarea>
                                    </div>
                                    <div class="form-text">
                                        Esta descrição ajudará os membros a entenderem melhor o propósito da equipa
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Coordenador -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coordenador" class="form-label fw-medium">
                                        Coordenador <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class='bx bx-user-check text-primary'></i>
                                        </span>
                                        <?php 
                                        error_log('Gerando select de coordenador com ' . count($utilizadores) . ' usuários');
                                        ?>
                                        <select class="form-select form-select-lg" id="coordenador" name="coordenador_id" required>
                                            <option value="" disabled selected>Selecione um coordenador</option>
                                            <?php 
                                            if (!empty($utilizadores)): 
                                                foreach ($utilizadores as $utilizador): 
                                                    error_log('Usuário disponível: ' . $utilizador['nome'] . ' (' . $utilizador['id_utilizador'] . ')');
                                            ?>
                                                <option value="<?php echo $utilizador['id_utilizador']; ?>">
                                                    <?php echo htmlspecialchars($utilizador['nome'] . ' (' . $utilizador['username'] . ')'); ?>
                                                </option>
                                            <?php 
                                                endforeach;
                                            else: 
                                            ?>
                                                <option value="" disabled>Nenhum usuário disponível</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-text">
                                        O coordenador será o responsável por gerenciar a equipa
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Membros -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="membros" class="form-label fw-medium">
                                        Membros da Equipa
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class='bx bx-group text-primary'></i>
                                        </span>
                                        <select class="form-select form-select-lg select2-multi" id="membros" name="membros[]" multiple="multiple">
                                            <?php foreach ($utilizadores as $utilizador): ?>
                                                <option value="<?php echo $utilizador['id_utilizador']; ?>">
                                                    <?php echo htmlspecialchars($utilizador['nome'] . ' (' . $utilizador['username'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-text">
                                        Selecione os membros da equipa. O coordenador será adicionado automaticamente.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light border-top p-4">
                        <button type="button" class="btn btn-lg btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class='bx bx-x me-2'></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-lg btn-primary px-4">
                            <i class='bx bx-save me-2'></i>Salvar Equipa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Log temporário para verificar os dados
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Coordenador select HTML:', document.getElementById('coordenador').innerHTML);
        console.log('Membros select HTML:', document.getElementById('membros').innerHTML);
    });
    </script>
    <script>
        console.log('jQuery carregado:', typeof jQuery !== 'undefined');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script>
        console.log('Popper.js carregado:', typeof Popper !== 'undefined');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('Bootstrap carregado:', typeof bootstrap !== 'undefined');
    </script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        console.log('Select2 carregado:', typeof $.fn.select2 !== 'undefined');
    </script>
    <!-- BoxIcons -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script>
        console.log('BoxIcons carregado:', typeof boxicons !== 'undefined');
    </script>
    <script>
        // Função para abrir o modal
        function abrirModalNovaEquipa() {
            // Limpar o formulário
            document.getElementById('formEquipa').reset();
            $('#equipaId').val('');
            $('#formAcao').val('criar');
            $('#modalTitulo').text('Nova Equipa');
            
            // Resetar selects
            if ($('#coordenador').data('select2')) {
                $('#coordenador').val(null).trigger('change');
            }
            
            if ($('#membros').data('select2')) {
                $('#membros').val(null).trigger('change');
            }
            
            // Limpar mensagens de erro
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            // Mostrar o modal sem backdrop
            var modalElement = document.getElementById('criarEquipaModal');
            var modal = new bootstrap.Modal(modalElement, {
                backdrop: false,
                keyboard: true
            });
            modal.show();
            
            // Focar no primeiro campo do formulário
            setTimeout(function() {
                $('#nome').focus();
            }, 100);
        }
        
        // Mostrar dados de depuração
        console.log('Usuários disponíveis:', <?php echo json_encode($utilizadores); ?>);
        
        // Inicializar tooltips e outros componentes
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM completamente carregado');
            
            // Inicializa tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover'
                });
            });
            
            // Inicializar Select2 para o coordenador
            if ($('#coordenador').length) {
                $('#coordenador').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Selecione um coordenador',
                    allowClear: true,
                    dropdownParent: $('#criarEquipaModal'),
                    minimumResultsForSearch: 10,
                    language: {
                        noResults: function() {
                            return "Nenhum resultado encontrado";
                        },
                        searching: function() {
                            return "A pesquisar...";
                        }
                    }
                });
            }
            
            // Inicializar Select2 múltiplo para membros
            if ($('#membros').length) {
                $('#membros').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Selecione os membros',
                    closeOnSelect: false,
                    dropdownParent: $('#criarEquipaModal'),
                    minimumResultsForSearch: 10,
                    language: {
                        noResults: function() {
                            return "Nenhum resultado encontrado";
                        },
                        searching: function() {
                            return "A pesquisar...";
                        }
                    }
                });
            }
            
            // Limpar formulário quando o modal for fechado
            var criarEquipaModal = document.getElementById('criarEquipaModal');
            if (criarEquipaModal) {
                criarEquipaModal.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('formEquipa').reset();
                    $('#equipaId').val('');
                    $('#formAcao').val('criar');
                    $('#modalTitulo').text('Nova Equipa');
                    
                    if ($('#coordenador').data('select2')) {
                        $('#coordenador').val(null).trigger('change');
                    }
                    
                    if ($('#membros').data('select2')) {
                        $('#membros').val(null).trigger('change');
                    }
                    
                    // Limpar mensagens de validação
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                });
            }
            
            // Remover o coordenador da lista de membros disponíveis
            if ($('#coordenador').length) {
                $('#coordenador').on('change', function() {
                    var coordenadorId = $(this).val();
                    var $membrosSelect = $('#membros');
                    
                    if (!$membrosSelect.length) return;
                    
                    // Obter os valores atualmente selecionados (exceto o coordenador)
                    var selectedValues = $membrosSelect.val() || [];
                    
                    // Habilitar todas as opções primeiro
                    $membrosSelect.find('option').prop('disabled', false);
                    
                    // Desabilitar a opção do coordenador selecionado
                    if (coordenadorId) {
                        $membrosSelect.find('option[value="' + coordenadorId + '"]').prop('disabled', true);
                        
                        // Remover o coordenador dos selecionados se estiver lá
                        var index = selectedValues.indexOf(coordenadorId);
                        if (index > -1) {
                            selectedValues.splice(index, 1);
                            $membrosSelect.val(selectedValues).trigger('change');
                        }
                    }
                    
                    // Forçar atualização do Select2
                    $membrosSelect.trigger('change.select2');
                });
            }
            
            // Inicializar Select2 múltiplo para membros
            $('#membros').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione os membros',
                closeOnSelect: false,
                dropdownParent: $('#criarEquipaModal')
            });
            
            // Limpar formulário quando o modal for fechado
            $('#criarEquipaModal').on('hidden.bs.modal', function () {
                document.getElementById('formEquipa').reset();
                $('#equipaId').val('');
                $('#formAcao').val('criar');
                $('#modalTitulo').text('Nova Equipa');
                $('#coordenador').val(null).trigger('change');
                $('#membros').val(null).trigger('change');
                
                // Limpar mensagens de validação
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });
            
            // Remover o coordenador da lista de membros disponíveis
            $('#coordenador').on('change', function() {
                var coordenadorId = $(this).val();
                var $membrosSelect = $('#membros');
                
                // Obter os valores atualmente selecionados (exceto o coordenador)
                var selectedValues = $membrosSelect.val() || [];
                
                // Habilitar todas as opções primeiro
                $membrosSelect.find('option').prop('disabled', false);
                
                // Desabilitar a opção do coordenador selecionado
                if (coordenadorId) {
                    $membrosSelect.find('option[value="' + coordenadorId + '"]').prop('disabled', true);
                    
                    // Remover o coordenador dos selecionados se estiver lá
                    var index = selectedValues.indexOf(coordenadorId);
                    if (index > -1) {
                        selectedValues.splice(index, 1);
                        $membrosSelect.val(selectedValues).trigger('change');
                    }
                }
                
                // Forçar atualização do Select2
                $membrosSelect.trigger('change.select2');
            });
            
            // Validação do formulário
            $('#formEquipa').on('submit', function(e) {
                var form = this;
                var coordenador = $('#coordenador').val();
                
                // Validar campos obrigatórios
                var isValid = true;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                if (!$('#nome').val().trim()) {
                    showError($('#nome'), 'Por favor, insira o nome da equipa');
                    isValid = false;
                }
                
                if (!coordenador) {
                    showError($('#coordenador'), 'Por favor, selecione um coordenador');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Rolar até o primeiro erro
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                }
                
                return isValid;
            });
            
            // Função auxiliar para exibir erros de validação
            function showError($element, message) {
                $element.addClass('is-invalid');
                $element.after('<div class="invalid-feedback">' + message + '</div>');
            }
        });
        
        // Função para editar equipe
        function editarEquipa(equipa) {
            console.log('Editando equipe:', equipa);
            
            // Preencher o formulário com os dados da equipe
            $('#equipaId').val(equipa.id);
            $('#nome').val(equipa.nome || '');
            $('#descricao').val(equipa.descricao || '');
            
            // Limpar seleções anteriores
            $('#coordenador').val(null).trigger('change');
            $('#membros').val(null).trigger('change');
            
            // Definir o coordenador
            if (equipa.coordenador_id) {
                $('#coordenador').val(equipa.coordenador_id).trigger('change');
            }
            
            // Atualizar título do modal
            $('#modalTitulo').text('Editar Equipa: ' + equipa.nome);
            $('#formAcao').val('editar');
            
            // Carregar membros da equipe via AJAX
            if (equipa.id) {
                $.ajax({
                    url: 'obter_membros_equipa.php',
                    type: 'GET',
                    data: { id: equipa.id },
                    dataType: 'json',
                    success: function(membros) {
                        console.log('Membros carregados:', membros);
                        var membrosIds = [];
                        
                        if (membros && membros.length > 0) {
                            membrosIds = membros.map(function(membro) {
                                return membro.id_utilizador;
                            });
                            
                            // Remover o coordenador dos membros
                            var coordenadorId = equipa.coordenador_id;
                            if (coordenadorId) {
                                var index = membrosIds.indexOf(parseInt(coordenadorId));
                                if (index > -1) {
                                    membrosIds.splice(index, 1);
                                }
                            }
                        }
                        
                        // Definir os membros selecionados
                        if (membrosIds.length > 0) {
                            $('#membros').val(membrosIds).trigger('change');
                        }
                        
                        // Abrir o modal após carregar os membros
                        var modal = new bootstrap.Modal(document.getElementById('criarEquipaModal'));
                        modal.show();
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao carregar membros:', error);
                        
                        // Abrir o modal mesmo com erro no carregamento dos membros
                        var modal = new bootstrap.Modal(document.getElementById('criarEquipaModal'));
                        modal.show();
                    }
                });
            } else {
                // Se não houver ID, apenas abrir o modal
                var modal = new bootstrap.Modal(document.getElementById('criarEquipaModal'));
                modal.show();
            }
        }
        
        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            if (confirm('Tem certeza que deseja excluir a equipa "' + nome + '"? Esta ação não pode ser desfeita.')) {
                // Criar formulário dinâmico para envio
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                var inputAcao = document.createElement('input');
                inputAcao.type = 'hidden';
                inputAcao.name = 'acao';
                inputAcao.value = 'excluir';
                
                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;
                
                form.appendChild(inputAcao);
                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
