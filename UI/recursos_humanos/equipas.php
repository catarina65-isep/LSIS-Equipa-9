<?php
// Iniciar a sessão
session_start();

// Incluir arquivos necessários
require_once __DIR__ . '/../../autoload.php';

// Verificar se o usuário está autenticado e tem perfil de RH (ID 2) ou Administrador (ID 1)
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit();
}

// Inicializar variáveis
$mensagem = '';
$tipoMensagem = 'info';
$equipas = [];

// Processar exclusão de equipe
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $equipaBLL = new EquipaBLL();
        $equipaId = (int)$_GET['id'];
        
        // Verificar se a equipe existe
        $equipa = $equipaBLL->obterEquipa($equipaId);
        if (!$equipa) {
            throw new Exception('Equipe não encontrada.');
        }
        
        // Remover a equipe
        $resultado = $equipaBLL->excluirEquipa($equipaId);
        
        if ($resultado) {
            $_SESSION['mensagem'] = 'Equipe excluída com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
            header('Location: equipas.php');
            exit();
        } else {
            throw new Exception('Não foi possível excluir a equipe.');
        }
    } catch (Exception $e) {
        $mensagem = 'Erro ao excluir equipe: ' . $e->getMessage();
        $tipoMensagem = 'danger';
        error_log('Erro ao excluir equipe: ' . $e->getMessage());
    }
}

try {
    // Inicializar BLLs
    $equipaBLL = new EquipaBLL();
    
    // Obter lista de equipes
    $equipas = $equipaBLL->listarEquipas();
    
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar os dados: ' . $e->getMessage();
    $tipoMensagem = 'danger';
    error_log('Erro em equipas.php: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Equipas - Portal do Colaborador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- BoxIcons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <style>
        .sidebar {
            width: 250px;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 1000;
            padding: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .page-header {
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
                transition: all 0.3s;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Gestão de Equipas</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Equipas</li>
                                </ol>
                            </nav>
                        </div>
                        <a href="criar_equipa.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Nova Equipa
                        </a>
                    </div>
                </div>

                <!-- Mensagens de feedback -->
                <div id="mensagens">
                    <?php if (isset($_SESSION['mensagem'])): ?>
                        <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['mensagem'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                        <?php 
                        unset($_SESSION['mensagem']);
                        unset($_SESSION['tipo_mensagem']);
                    endif; ?>
                    
                    <?php if (!empty($mensagem)): ?>
                        <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show" role="alert">
                            <?= $mensagem ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="container-fluid">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Lista de Equipas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaEquipas">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Coordenador</th>
                                            <th>Membros</th>
                                            <th>Estado</th>
                                            <th class="text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($equipas)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Nenhuma equipa registada.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($equipas as $equipa): ?>
                                                <tr data-id="<?= $equipa['id_equipa'] ?>">
                                                    <td><?= htmlspecialchars($equipa['nome']) ?></td>
                                                    <td><?= htmlspecialchars($equipa['coordenador_nome'] ?? 'Não definido') ?></td>
                                                    <td><?= $equipa['total_membros'] ?? 0 ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $equipa['ativo'] ? 'success' : 'secondary' ?>">
                                                            <?= $equipa['ativo'] ? 'Ativo' : 'Inativo' ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-primary btn-editar" data-id="<?= $equipa['id_equipa'] ?>" title="Editar">
                                                            <i class="bx bx-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="<?= $equipa['id_equipa'] ?>" title="Eliminar">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incluir o modal de equipe -->
                <?php include 'includes/modal_equipa.php'; ?>

                <!-- Scripts -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
                
                <!-- Inicialização do DataTable -->
                <script>
                $(document).ready(function() {
                    // Inicializar DataTable
                    var table = $('#tabelaEquipas').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                            search: "Pesquisar:",
                            lengthMenu: "Mostrar _MENU_ registos por página",
                            zeroRecords: "Nenhum registo encontrado",
                            info: "A mostrar página _PAGE_ de _PAGES_",
                            infoEmpty: "Nenhum registo disponível",
                            infoFiltered: "(filtrado de _MAX_ registos no total)"
                        },
                        order: [[0, 'asc']],
                        pageLength: 10,
                        responsive: true,
                        processing: true,
                        serverSide: false
                    });

                    // Inicializar tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();

                    // Evento de clique para o botão de excluir
                    $(document).on('click', '.btn-excluir', function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var linha = $(this).closest('tr');
                        
                        if (confirm('Tem certeza que deseja excluir esta equipa? Esta ação não pode ser desfeita.')) {
                            $.ajax({
                                url: 'processar_equipa.php',
                                type: 'POST',
                                data: {
                                    acao: 'excluir',
                                    id: id
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.sucesso) {
                                        // Remover a linha da tabela
                                        table.row(linha).remove().draw(false);
                                        // Mostrar mensagem de sucesso
                                        var alerta = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                            response.mensagem +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button></div>';
                                        $('#mensagens').html(alerta);
                                    } else {
                                        alert('Erro ao excluir: ' + (response.erro || 'Erro desconhecido'));
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert('Erro na requisição: ' + error);
                                }
                            });
                        }
                    });

                    // Evento de clique para o botão de editar
                    $(document).on('click', '.btn-editar', function() {
                        var id = $(this).data('id');
                        // Redirecionar para a página de edição
                        window.location.href = 'editar_equipa.php?id=' + id;
                    });
                });
                </script>
                
                <!-- Incluir o script de gerenciamento de equipes -->
                <script src="js/equipas.js"></script>
            </main>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione uma opção',
            allowClear: true,
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                },
                searching: function() {
                    return "A pesquisar...";
                },
                inputTooShort: function(args) {
                    return "Digite pelo menos " + args.minimum + " caracteres";
                }
            }
        });
        
        // Ajustar z-index do modal para ficar acima da barra de navegação
        $('.modal').on('show.bs.modal', function () {
            // Força o modal a ficar acima de tudo
            $('.modal-backdrop').css('z-index', '1070');
            $(this).css('z-index', '1080');
            
            // Desabilita o scroll do body quando o modal estiver aberto
            $('body').addClass('modal-open');
        });
        
        // Ao fechar o modal
        $('.modal').on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open');
        });
        
        // Ajuste para o sidebar
        $('.sidebar').css('z-index', '1050');
        
        // Ajuste para o conteúdo principal
        $('.main-content').css('position', 'relative');
    });
    </script>
</body>
</html>
