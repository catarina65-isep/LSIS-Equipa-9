<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado e tem permissão de RH ou Admin
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Incluir o BLL de campos personalizados
require_once __DIR__ . '/../../BLL/campoPersonalizadoBLL.php';

// Configurações da página
$page_title = "Gestão de Campos Personalizados";
$isAdmin = ($_SESSION['id_perfilacesso'] == 1);
$isRH = ($_SESSION['id_perfilacesso'] == 2);
$podeEditar = ($isAdmin || $isRH);

// Tipos de campo disponíveis
$tiposCampo = [
    'texto' => 'Texto',
    'numero' => 'Número',
    'data' => 'Data',
    'email' => 'E-mail',
    'telefone' => 'Telefone',
    'selecao' => 'Seleção Única',
    'multiselecao' => 'Seleção Múltipla',
    'sim_nao' => 'Sim/Não',
    'arquivo' => 'Anexo de Arquivo',
    'cep' => 'CEP',
    'cpf' => 'CPF',
    'cnpj' => 'CNPJ',
    'checkbox' => 'Checkbox',
    'radio' => 'Botão de Rádio',
    'textarea' => 'Área de Texto',
    'mod99' => 'Mod 99',
    'nif' => 'NIF',
    'niss' => 'NISS',
    'cartaocidadao' => 'Cartão de Cidadão'
];

// Inicializar mensagens
$mensagem = '';
$tipoMensagem = '';

// Obter a instância do BLL (usando o padrão Singleton)
$campoBLL = CampoPersonalizadoBLL::getInstance();

// Obter lista de campos do banco de dados
try {
    $resultado = $campoBLL->obterCampos();
    if ($resultado['sucesso']) {
        $campos = $resultado['dados'];
    } else {
        throw new Exception($resultado['erro']);
    }
} catch (Exception $e) {
    $mensagem = "Erro ao carregar campos: " . $e->getMessage();
    $tipoMensagem = 'danger';
    $campos = [];
}

// Obter lista de categorias
$categorias = [
    'dados_pessoais' => 'Dados Pessoais',
    'contato' => 'Informações de Contato',
    'documentos' => 'Documentos',
    'endereco' => 'Endereço',
    'familiar' => 'Familiar',
    'academico' => 'Acadêmico',
    'profissional' => 'Profissional',
    'beneficios' => 'Benefícios',
    'saude' => 'Saúde',
    'outros' => 'Outros'
];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 4px;
        }
        
        .table th {
            vertical-align: middle;
        }
        
        .btn-adicionar {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.25rem;
        }
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .form-section h5 {
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include __DIR__ . '/includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Mensagens -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show mb-4" role="alert">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Cabeçalho da Página -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
                    <?php if ($podeEditar): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#campoModal">
                            <i class="fas fa-plus me-1"></i> Novo Campo
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Cards de Resumo -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total de Campos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($campos) ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Campos Obrigatórios</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= count(array_filter($campos, fn($campo) => $campo['obrigatorio'])) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-asterisk fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Categorias Únicas</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= count(array_unique(array_column($campos, 'categoria'))) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-folder fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Tipos Diferentes</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= count(array_unique(array_column($campos, 'tipo'))) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
                        <button class="btn btn-sm btn-link" id="limparFiltros">
                            <i class="fas fa-sync-alt me-1"></i>Limpar Filtros
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="filtroTipo" class="form-label">Tipo de Campo</label>
                                <select class="form-select" id="filtroTipo">
                                    <option value="">Todos os Tipos</option>
                                    <?php foreach ($tiposCampo as $valor => $rotulo): ?>
                                        <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="filtroCategoria" class="form-label">Categoria</label>
                                <select class="form-select" id="filtroCategoria">
                                    <option value="">Todas as Categorias</option>
                                    <?php foreach ($categorias as $valor => $rotulo): ?>
                                        <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="filtroStatus" class="form-label">Status</label>
                                <select class="form-select" id="filtroStatus">
                                    <option value="">Todos</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="filtroGeral" placeholder="Pesquisar por nome, rótulo ou descrição...">
                                    <button class="btn btn-primary" type="button" id="btnPesquisar">
                                        <i class="fas fa-search"></i> Pesquisar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Campos -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="tabelaCampos" class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Rótulo</th>
                                        <th>Tipo</th>
                                        <th>Categoria</th>
                                        <th class="text-center">Obrigatório</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($campos as $campo): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($campo['nome']) ?></td>
                                        <td><?= htmlspecialchars($campo['rotulo']) ?></td>
                                        <td><?= htmlspecialchars($tiposCampo[$campo['tipo']] ?? $campo['tipo']) ?></td>
                                        <td><?= htmlspecialchars($categorias[$campo['categoria']] ?? $campo['categoria']) ?></td>
                                        <td class="text-center">
                                            <?php if ($campo['obrigatorio']): ?>
                                                <span class="badge bg-danger">Sim</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Não</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($campo['ativo']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($podeEditar): ?>
                                                <?php
                                                    $idCampo = $campo['id'] ?? $campo['id_campo'] ?? 0;
                                                ?>
                                                <button class="btn btn-sm btn-warning btn-editar" 
                                                        data-id="<?= $idCampo ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#campoModal"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php
                                                    $nomeCampo = isset($campo['nome']) ? str_replace("'", "\\'", $campo['nome']) : '';
                                                    $idCampo = $campo['id'] ?? $campo['id_campo'] ?? 0; // Tenta ambas as chaves
                                                    $urlExcluir = "excluir_campo.php?id=" . urlencode($idCampo);
                                                    $mensagem = "Tem certeza que deseja excluir o campo " . addslashes($nomeCampo) . "? Esta ação não pode ser desfeita.";
                                                ?>
                                                <a href="#" 
                                                   onclick="if(confirm('<?= $mensagem ?>')) { window.location.href='<?= $urlExcluir ?>'; } return false;"
                                                   class="btn btn-sm btn-danger"
                                                   title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Adicionar/Editar Campo -->
    <div class="modal fade" id="campoModal" tabindex="-1" aria-labelledby="campoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="campoModalLabel">Adicionar Novo Campo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formCampo" method="POST" action="">
                    <input type="hidden" name="acao" id="acao" value="criar">
                    <input type="hidden" name="id" id="campoId">
                    
                    <div class="modal-body">
                        <div class="form-section">
                            <h5>Informações Básicas</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome do Campo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nome" name="nome" required 
                                           pattern="[a-z][a-z0-9_]*" title="Use apenas letras minúsculas, números e sublinhados">
                                    <div class="form-text">Identificador único para o campo (ex: telefone_emergencia)</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="rotulo" class="form-label">Rótulo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="rotulo" name="rotulo" required>
                                    <div class="form-text">Texto que será exibido no formulário (ex: Telefone de Emergência)</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tipo" class="form-label">Tipo de Campo <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <?php foreach ($tiposCampo as $valor => $rotulo): ?>
                                            <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <?php foreach ($categorias as $valor => $rotulo): ?>
                                            <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                                    <div class="form-text">Descrição detalhada do campo (opcional)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h5>Configurações</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="obrigatorio" name="obrigatorio">
                                        <label class="form-check-label" for="obrigatorio">Campo Obrigatório</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="requer_comprovativo" name="requer_comprovativo">
                                        <label class="form-check-label" for="requer_comprovativo">Requer Comprovativo</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                                        <label class="form-check-label" for="ativo">Ativo</label>
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label class="form-label">Visível para</label>
                                    <select class="form-select select2" id="visivel_para" name="visivel_para[]" multiple>
                                        <option value="1">Administrador</option>
                                        <option value="2">RH</option>
                                        <option value="3">Colaborador</option>
                                    </select>
                                    <div class="form-text">Selecione os perfis que podem visualizar este campo</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section opcoes-campo d-none">
                            <h5>Opções do Campo</h5>
                            <div class="mb-3">
                                <label for="opcoes" class="form-label">Opções (uma por linha)</label>
                                <textarea class="form-control" id="opcoes" name="opcoes" rows="4" placeholder="Exemplo:
Opção 1
Opção 2
Opção 3"></textarea>
                                <div class="form-text">Informe uma opção por linha. Para pares chave-valor, use o formato: chave:Valor</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Campo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="confirmarExclusaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o campo <strong id="nomeCampoExcluir"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Esta ação não pode ser desfeita!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</button>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .table-responsive {
            border-radius: 0.35rem;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #5a5c69;
        }
        
        .table > :not(:first-child) {
            border-top: none;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        .form-section {
            background-color: #f8f9fc;
            border-radius: 0.35rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .form-section h5 {
            color: #4e73df;
            font-size: 1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable com configurações estendidas
            var tabela = $('#tabelaCampos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                    search: "_INPUT_",
                    searchPlaceholder: "Pesquisar...",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "Nenhum registro encontrado",
                    info: "Mostrando página _PAGE_ de _PAGES_",
                    infoEmpty: "Nenhum registro disponível",
                    infoFiltered: "(filtrado de _MAX_ registros totais)",
                    paginate: {
                        first: "Primeira",
                        last: "Última",
                        next: "Próxima",
                        previous: "Anterior"
                    }
                },
                responsive: true,
                order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columnDefs: [
                    { orderable: false, targets: [6] }, // Desabilitar ordenação na coluna de ações
                    { className: "text-center", targets: [4, 5] } // Centralizar colunas de status
                ],
                initComplete: function() {
                    // Adicionar classes de estilo após a inicialização
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    $('.dataTables_paginate .paginate_button').addClass('btn-sm');
                }
            });
            
            // Aplicar filtros
            function aplicarFiltros() {
                tabela.draw();
            }
            
            // Filtro por tipo
            $('#filtroTipo').on('change', function() {
                tabela.column(2).search(this.value).draw();
            });
            
            // Filtro por categoria
            $('#filtroCategoria').on('change', function() {
                tabela.column(3).search(this.value).draw();
            });
            
            // Filtro por status
            $('#filtroStatus').on('change', function() {
                if (this.value === '') {
                    tabela.column(5).search('').draw();
                } else {
                    tabela.column(5).search(this.value === '1' ? 'Ativo' : 'Inativo', true, false).draw();
                }
            });
            
            // Filtro de busca geral
            $('#filtroGeral').on('keyup', function() {
                tabela.search(this.value).draw();
            });
            
            // Botão de pesquisa
            $('#btnPesquisar').on('click', function() {
                aplicarFiltros();
            });
            
            // Limpar todos os filtros
            $('#limparFiltros').on('click', function() {
                $('#filtroTipo, #filtroCategoria, #filtroStatus').val('').trigger('change');
                $('#filtroGeral').val('');
                tabela.search('').columns().search('').draw();
            });
            
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione as opções',
                allowClear: true
            });
            
            // Mostrar/ocultar opções de acordo com o tipo de campo
            $('#tipo').on('change', function() {
                var tipo = $(this).val();
                if (['selecao', 'multiselecao', 'radio', 'checkbox'].includes(tipo)) {
                    $('.opcoes-campo').removeClass('d-none');
                } else {
                    $('.opcoes-campo').addClass('d-none');
                }
            });
            
            // Abrir modal para edição
            $(document).on('click', '.btn-editar', function() {
                var id = $(this).data('id');
                var tr = $(this).closest('tr');
                
                // Preencher o formulário com os dados da linha
                $('#nome').val(tr.find('td:eq(0)').text().trim());
                $('#rotulo').val(tr.find('td:eq(1)').text().trim());
                
                // Encontrar o tipo correto baseado no texto exibido
                var tipoTexto = tr.find('td:eq(2)').text().trim();
                var tipoValor = Object.entries(<?= json_encode($tiposCampo) ?>).find(([key, value]) => value === tipoTexto)?.[0] || tipoTexto;
                $('#tipo').val(tipoValor).trigger('change');
                
                // Encontrar a categoria correta baseada no texto exibido
                var categoriaTexto = tr.find('td:eq(3)').text().trim();
                var categoriaValor = Object.entries(<?= json_encode($categorias) ?>).find(([key, value]) => value === categoriaTexto)?.[0] || categoriaTexto;
                $('#categoria').val(categoriaValor);
                
                // Verificar se é obrigatório
                $('#obrigatorio').prop('checked', tr.find('td:eq(4)').find('.badge').hasClass('bg-danger'));
                
                // Verificar se está ativo
                $('#ativo').prop('checked', tr.find('td:eq(5)').find('.badge').hasClass('bg-success'));
                
                // Configurar o formulário para edição
                $('#campoModalLabel').text('Editar Campo');
                $('#acao').val('editar');
                $('#campoId').val(id);
                
                // Aqui você pode adicionar uma chamada AJAX para buscar mais detalhes do campo
                // caso necessário (como opções, visibilidade, etc.)
                
                // Exemplo de como seria a chamada AJAX para buscar detalhes adicionais:
                /*
                $.ajax({
                    url: 'obter_campo.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Preencher os campos adicionais aqui
                            $('#descricao').val(response.descricao || '');
                            // ... outros campos
                        } else {
                            alert('Erro ao carregar os dados do campo: ' + (response.message || 'Erro desconhecido'));
                        }
                    },
                    error: function() {
                        alert('Erro na requisição. Tente novamente.');
                    }
                });
                */
            });
            
            // Submeter o formulário de criação/edição
            $('#formCampo').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var btn = form.find('button[type="submit"]');
                var formData = new FormData(form[0]);
                
                // Adicionar logs para depuração
                console.log('Formulário submetido');
                console.log('Dados do formulário:');
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // Desabilitar o botão para evitar múltiplos envios
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
                
                // Enviar os dados do formulário
                $.ajax({
                    url: 'processar_campo.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Resposta do servidor (sucesso):', response);
                        if (response.success) {
                            // Se houver redirecionamento, redireciona
                            if (response.redirect) {
                                window.location.href = response.redirect;
                                return;
                            }
                            
                            // Se não houver redirecionamento, fecha o modal e recarrega a página
                            var modal = bootstrap.Modal.getInstance(document.getElementById('campoModal'));
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Recarrega a página para mostrar a mensagem de sucesso
                            window.location.reload();
                        } else {
                            var erro = response.erro || 'Ocorreu um erro ao processar sua solicitação.';
                            console.error('Erro na resposta:', erro);
                            mostrarMensagem('Erro!', erro, 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusText: xhr.statusText
                        });
                        
                        var errorMessage = 'Erro na requisição. Verifique o console para mais detalhes.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Erro ao analisar resposta de erro:', e);
                        }
                        
                        mostrarMensagem('Erro!', errorMessage, 'danger');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('Salvar Campo');
                    }
                });
            });
            
            // Limpar formulário ao abrir o modal para adicionar
            $('#campoModal').on('hidden.bs.modal', function () {
                $('#formCampo')[0].reset();
                $('#campoModalLabel').text('Adicionar Novo Campo');
                $('#acao').val('criar');
                $('.select2').val(null).trigger('change');
                $('.opcoes-campo').addClass('d-none');
                // Remover mensagens de erro
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });
            
            // Função para exibir mensagens
            function mostrarMensagem(titulo, mensagem, tipo) {
                // Criar elemento de alerta
                var alertHtml = `
                    <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                        <strong>${titulo}</strong> ${mensagem}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                `;
                
                // Inserir o alerta no início do conteúdo principal
                $('.main-content').prepend(alertHtml);
                
                // Rolagem suave para o topo
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                
                // Remover o alerta após 5 segundos
                setTimeout(function() {
                    $('.alert').fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
</body>
</html>