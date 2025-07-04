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
$coordenadores = [];
$funcionarios = [];

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
    $utilizadorBLL = new UtilizadorBLL();
    
    error_log('Iniciando carregamento das equipes...');
    
    // Obter lista de equipes
    $equipas = $equipaBLL->listarEquipas();
    error_log('Equipas carregadas: ' . print_r($equipas, true));
    
    // Verificar se há equipes
    if (empty($equipas)) {
        error_log('Nenhuma equipe encontrada no banco de dados');
    } else {
        error_log('Total de equipes encontradas: ' . count($equipas));
    }
    
    // Obter lista de coordenadores
    $coordenadores = $utilizadorBLL->obterCoordenadores();
    error_log('Coordenadores carregados: ' . print_r($coordenadores, true));
    
    // Obter lista de funcionários
    $funcionarios = $utilizadorBLL->obterFuncionarios();
    error_log('Funcionários carregados: ' . print_r($funcionarios, true));
    
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar os dados: ' . $e->getMessage();
    $tipoMensagem = 'danger';
    error_log('Erro em equipas.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        /* Layout da Página */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            background: #1a1f33;
            overflow-y: auto;
            transition: all 0.3s;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f5f7fb;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
            width: calc(100% - 250px);
        }
        
        /* Ajuste para dispositivos móveis */
        @media (max-width: 992px) {
            .sidebar {
                margin-left: -250px;
                z-index: 1050;
            }
            
            .sidebar.active {
                margin-left: 0;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.active {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
            
            /* Ajuste para o botão de toggle no mobile */
            .navbar-toggler {
                z-index: 1051;
            }
        }
        
        /* Garantir que o conteúdo não fique atrás do footer */
        body {
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
            padding-right: 0 !important; /* Remove o padding do body quando o modal estiver aberto */
        }
        
        /* Ajustes para o modal */
        .modal {
            z-index: 1080 !important; /* Garante que o modal fique acima da sidebar */
        }
        
        .modal-backdrop {
            z-index: 1070 !important; /* Garante que o backdrop fique acima da sidebar */
        }
        
        /* Garante que o modal fique centralizado verticalmente */
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
        
        /* Ajuste para o conteúdo do modal */
        .modal-content {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        /* Estilos dos Cards */
        .team-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 0.75rem;
            overflow: hidden;
            height: 100%;
            background: #fff;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
            padding: 1rem 1.25rem;
        }
        
        .team-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
            font-size: 1.1rem;
        }
        
        .team-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }
        
        .member-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .members-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .btn-action {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0.5rem;
        }
        
        /* Estilos do Header */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        
        /* Estilos para o Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border-radius: 0.5rem;
            border-color: #dee2e6;
        }
        
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            border-radius: 0.35rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
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
                            <h1 class="h3 mb-1 fw-bold text-gray-800">Gestão de Equipas</h1>
                            <p class="mb-0 text-muted">Gerencie as equipas da sua organização</p>
                        </div>
                        <a href="criar_equipa.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Nova Equipa
                        </a>
                    </div>
                </div>

                <!-- Mensagens de Feedback -->
                <?php if (isset($_SESSION['mensagem'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['tipo_mensagem']; ?> alert-dismissible fade show mb-4">
                        <?php 
                        echo $_SESSION['mensagem']; 
                        unset($_SESSION['mensagem']);
                        unset($_SESSION['tipo_mensagem']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endif; ?>

                <!-- Conteúdo Principal -->
                <div class="container-fluid">
                    <div class="row">
                        <?php if (empty($equipas)): ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Nenhuma equipa cadastrada. Clique em "Nova Equipa" para começar.
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($equipas as $equipa): ?>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card team-card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title team-name"><?= htmlspecialchars($equipa['nome']) ?></h5>
                                            
                                            <?php if (!empty($equipa['descricao'])): ?>
                                                <p class="team-description"><?= htmlspecialchars($equipa['descricao']) ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex align-items-center mb-3">
                                                <small class="text-muted me-2">Coordenador:</small>
                                                <?php if (!empty($equipa['coordenador_foto'])): ?>
                                                    <img src="<?= htmlspecialchars($equipa['coordenador_foto']) ?>" alt="Coordenador" class="member-avatar">
                                                <?php else: ?>
                                                    <div class="member-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                                                        <?= substr($equipa['coordenador_nome'] ?? '?', 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="ms-2"><?= htmlspecialchars($equipa['coordenador_nome'] ?? 'Não definido') ?></span>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i> 
                                                    <?= $equipa['total_membros'] ?? 0 ?> membro(s)
                                                </small>
                                                <div>
                                                    <a href="editar_equipa.php?id=<?= $equipa['id_equipa'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?action=delete&id=<?= $equipa['id_equipa'] ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Excluir"
                                                       onclick="return confirm('Tem certeza que deseja excluir esta equipa? Esta ação não pode ser desfeita.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Inicializar Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione as opções',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    },
                    searching: function() {
                        return "Buscando...";
                    },
                    inputTooShort: function(args) {
                        return "Digite pelo menos " + args.minimum + " caracteres";
                    }
                }
            });
        });

        // Função para confirmar exclusão
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir esta equipa? Esta ação não pode ser desfeita.')) {
                window.location.href = '?action=delete&id=' + id;
            }
            return false;
        }
        
        // Ajustar z-index do modal para ficar acima da barra de navegação
        $(document).ready(function() {
            // Ajuste para modais
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
            
            // Ajuste para o container principal
            $('.container-fluid').css('position', 'relative');
        });
    </script>
</body>
</html>
