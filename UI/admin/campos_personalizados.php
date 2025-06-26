<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

$page_title = "Campos Personalizados - Tlantic";

// Tipos de campos permitidos
$tiposCampos = [
    'texto' => 'Texto',
    'numero' => 'Número',
    'data' => 'Data',
    'email' => 'E-mail',
    'telefone' => 'Telefone',
    'cep' => 'CEP',
    'cpf' => 'CPF',
    'cnpj' => 'CNPJ',
    'select' => 'Seleção',
    'checkbox' => 'Checkbox',
    'radio' => 'Botão de Rádio',
    'textarea' => 'Área de Texto',
    'arquivo' => 'Upload de Arquivo',
    'mod99' => 'Mod 99',
    'nif' => 'NIF',
    'niss' => 'NISS',
    'cartaocidadao' => 'Cartão de Cidadão'
];

// Status dos campos
$statusCampos = [
    'ativo' => 'Ativo',
    'inativo' => 'Inativo'
];

// Categorias de campos
$categoriasCampos = [
    'pessoal' => 'Dados Pessoais',
    'contato' => 'Contato',
    'documentos' => 'Documentos',
    'endereco' => 'Endereço',
    'familiar' => 'Familiar',
    'academico' => 'Acadêmico',
    'profissional' => 'Profissional',
    'beneficios' => 'Benefícios',
    'saude' => 'Saúde',
    'outros' => 'Outros'
];

// Processar formulário de adição/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar e processar os dados do formulário
    $dados = [
        'nome' => $_POST['nomeCampo'] ?? '',
        'tipo' => $_POST['tipoCampo'] ?? '',
        'rotulo' => $_POST['rotuloCampo'] ?? '',
        'placeholder' => $_POST['placeholderCampo'] ?? '',
        'valor_padrao' => $_POST['valorPadrao'] ?? '',
        'obrigatorio' => isset($_POST['campoObrigatorio']) ? 1 : 0,
        'ativo' => isset($_POST['campoAtivo']) ? 1 : 0,
        'categoria' => $_POST['categoriaCampo'] ?? 'outros',
        'requer_comprovativo' => isset($_POST['requerComprovativo']) ? 1 : 0,
        'visivel_para' => $_POST['visivelPara'] ?? ['admin'],
        'editavel_por' => $_POST['editavelPor'] ?? ['admin'],
        'ajuda' => $_POST['ajudaCampo'] ?? '',
        'opcoes' => []
    ];

    // Processar opções para campos de seleção
    if (in_array($dados['tipo'], ['select', 'radio', 'checkbox'])) {
        $opcoes = [];
        foreach ($_POST['opcao_valor'] as $index => $valor) {
            if (!empty($valor) && !empty($_POST['opcao_rotulo'][$index])) {
                $opcoes[$valor] = $_POST['opcao_rotulo'][$index];
            }
        }
        $dados['opcoes'] = $opcoes;
    }

    // Aqui você deve adicionar a lógica para salvar no banco de dados
    // $salvo = salvarCampoPersonalizado($dados);
    
    // Redirecionar com mensagem de sucesso/erro
    // header('Location: campos_personalizados.php?status=' . ($salvo ? 'sucesso' : 'erro'));
    // exit;
}

// Obter lista de campos personalizados
// $campos = obterCamposPersonalizados();
$campos = []; // Simulação de dados
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            border-top: none;
        }
        
        .form-control, .form-select, .form-check-input {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
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
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm rounded">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Campos Personalizados</h1>
                        <p class="mb-0 text-muted">Gerencie os campos personalizados da ficha de colaborador</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoCampoModal">
                            <i class='bx bx-plus'></i> Novo Campo
                        </button>
                    </div>
                </div>

                <div class="container-fluid px-4">
                    <!-- Filtros -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <form class="row g-3">
                                <div class="col-md-4">
                                    <label for="tipoFiltroCampo" class="form-label">Tipo de Campo</label>
                                    <select class="form-select" id="tipoFiltroCampo">
                                        <option value="" selected>Todos os Tipos</option>
                                        <?php foreach ($tiposCampos as $valor => $rotulo): ?>
                                            <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="statusCampo" class="form-label">Status</label>
                                    <select class="form-select" id="statusCampo">
                                        <option value="" selected>Todos</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class='bx bx-filter-alt'></i> Filtrar
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        Limpar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Tabela de Campos -->
                    <div class="card border-0 shadow-sm">
                        <!-- Cabeçalho com Filtros -->
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                                <h5 class="card-title mb-2 mb-md-0">
                                    <i class='bx bx-list-ul text-primary me-2'></i>Lista de Campos Personalizados
                                </h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#campoModal">
                                    <i class='bx bx-plus-circle me-1'></i> Adicionar Campo
                                </button>
                            </div>
                            
                            <!-- Filtros -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="filtroTipo" class="form-label">Tipo de Campo</label>
                                    <select id="filtroTipo" class="form-select form-select-sm">
                                        <option value="" selected>Todos os tipos</option>
                                        <option value="Texto">Texto</option>
                                        <option value="Número">Número</option>
                                        <option value="Data">Data</option>
                                        <option value="E-mail">E-mail</option>
                                        <option value="Telefone">Telefone</option>
                                        <option value="CPF">CPF</option>
                                        <option value="CNPJ">CNPJ</option>
                                        <option value="NIF">NIF</option>
                                        <option value="NISS">NISS</option>
                                        <option value="Seleção">Seleção</option>
                                        <option value="Checkbox">Checkbox</option>
                                        <option value="Radio">Botão de Rádio</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filtroStatus" class="form-label">Status</label>
                                    <select id="filtroStatus" class="form-select form-select-sm">
                                        <option value="todos" selected>Todos</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filtroCategoria" class="form-label">Categoria</label>
                                    <select id="filtroCategoria" class="form-select form-select-sm">
                                        <option value="" selected>Todas as categorias</option>
                                        <?php foreach ($categoriasCampos as $valor => $rotulo): ?>
                                            <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button id="limparFiltros" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="tooltip" title="Limpar todos os filtros">
                                        <i class='bx bx-refresh me-1'></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Corpo da Tabela -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tabelaCampos" class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">Nome</th>
                                            <th width="10%">Tipo</th>
                                            <th width="25%">Rótulo</th>
                                            <th width="15%">Categoria</th>
                                            <th width="10%">Obrigatório</th>
                                            <th width="10%">Status</th>
                                            <th width="15%" class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Exemplo de dados estáticos para demonstração -->
                                        <tr>
                                            <td>telefone_emergencia</td>
                                            <td>Telefone</td>
                                            <td>Telefone de Emergência</td>
                                            <td>Contato</td>
                                            <td><span class="badge bg-success">Sim</span></td>
                                            <td><span class="badge bg-success">Ativo</span></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary btn-editar" data-id="1">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="1" data-nome="telefone_emergencia">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Visualizar histórico">
                                                    <i class='bx bx-history'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>data_nascimento</td>
                                            <td>Data</td>
                                            <td>Data de Nascimento</td>
                                            <td>Dados Pessoais</td>
                                            <td><span class="badge bg-success">Sim</span></td>
                                            <td><span class="badge bg-success">Ativo</span></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary btn-editar" data-id="2">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="2" data-nome="data_nascimento">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Visualizar histórico">
                                                    <i class='bx bx-history'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>nif</td>
                                            <td>NIF</td>
                                            <td>NIF</td>
                                            <td>Documentos</td>
                                            <td><span class="badge bg-success">Sim</span></td>
                                            <td><span class="badge bg-success">Ativo</span></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary btn-editar" data-id="3">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="3" data-nome="nif">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Visualizar histórico">
                                                    <i class='bx bx-history'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>niss</td>
                                            <td>NISS</td>
                                            <td>Número de Identificação da Segurança Social</td>
                                            <td>Documentos</td>
                                            <td><span class="badge bg-success">Sim</span></td>
                                            <td><span class="badge bg-secondary">Inativo</span></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary btn-editar" data-id="4">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="4" data-nome="niss">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Visualizar histórico">
                                                    <i class='bx bx-history'></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Rodapé da Tabela -->
                        <div class="card-footer bg-white border-top py-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <div class="mb-2 mb-md-0">
                                    <span class="text-muted">
                                        Mostrando <strong>1</strong> a <strong>4</strong> de <strong>4</strong> registros
                                    </span>
                                </div>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle btn-exportar-geral" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-export me-1'></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item btn-exportar-pdf" type="button"><i class='bx bxs-file-pdf me-2'></i>Exportar para PDF</button></li>
                                        <li><button class="dropdown-item btn-exportar-excel" type="button"><i class='bx bxs-file-export me-2'></i>Exportar para Excel</button></li>
                                        <li><button class="dropdown-item btn-exportar-csv" type="button"><i class='bx bxs-file-export me-2'></i>Exportar para CSV</button></li>
                                    </ul>
                                </div>
                                
                                <nav class="mt-2 mt-md-0">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                                <i class='bx bx-chevron-left'></i>
                                            </a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">
                                                <i class='bx bx-chevron-right'></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer mt-auto py-3 bg-light">
                    <div class="container-fluid px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                &copy; <?= date('Y') ?> Tlantic - Todos os direitos reservados
                            </div>
                            <div>
                                <span class="text-muted">Versão 1.0.0</span>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Modal Novo/Editar Campo -->
    <div class="modal fade" id="campoModal" tabindex="-1" aria-labelledby="campoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="campoModalLabel">Adicionar Novo Campo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCampo">
                        <input type="hidden" id="campoId" name="campoId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomeCampo" class="form-label">Nome do Campo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomeCampo" name="nomeCampo" required>
                                <div class="form-text">Use apenas letras minúsculas e underline (_)</div>
                            </div>
                            <div class="col-md-6">
                                <label for="tipoCampo" class="form-label">Tipo de Campo <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipoCampo" name="tipoCampo" required>
                                    <option value="">Selecione um tipo...</option>
                                    <?php foreach ($tiposCampos as $valor => $rotulo): ?>
                                        <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="rotuloCampo" class="form-label">Rótulo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rotuloCampo" name="rotuloCampo" required>
                                <div class="form-text">Nome que será exibido no formulário</div>
                            </div>
                            <div class="col-md-6">
                                <label for="categoriaCampo" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoriaCampo" name="categoriaCampo" required>
                                    <?php foreach ($categoriasCampos as $valor => $rotulo): ?>
                                        <option value="<?= $valor ?>"><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="placeholderCampo" class="form-label">Placeholder</label>
                                <input type="text" class="form-control" id="placeholderCampo">
                            </div>
                            <div class="col-md-6">
                                <label for="valorPadrao" class="form-label">Valor Padrão</label>
                                <input type="text" class="form-control" id="valorPadrao">
                            </div>
                            <div class="col-12" id="opcoesContainer" style="display: none;">
                                <label class="form-label">Opções</label>
                                <div id="opcoesCampos">
                                    <div class="row g-2 mb-2">
                                        <div class="col-5">
                                            <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo">
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="adicionarOpcao">
                                    <i class='bx bx-plus'></i> Adicionar Opção
                                </button>
                            </div>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Permissões</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Visível para</label>
                                                <select class="form-select select2" name="visivelPara[]" multiple>
                                                    <option value="admin" selected>Administrador</option>
                                                    <option value="rh">Recursos Humanos</option>
                                                    <option value="coordenador">Coordenador</option>
                                                    <option value="colaborador">Colaborador</option>
                                                    <option value="convidado">Convidado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Editável por</label>
                                                <select class="form-select select2" name="editavelPor[]" multiple>
                                                    <option value="admin" selected>Administrador</option>
                                                    <option value="rh">Recursos Humanos</option>
                                                    <option value="coordenador">Coordenador</option>
                                                    <option value="colaborador">Colaborador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Configurações Avançadas</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="requerComprovativo" name="requerComprovativo">
                                                    <label class="form-check-label" for="requerComprovativo">Requer comprovativo</label>
                                                    <div class="form-text">Exige upload de documento para validar este campo</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="validarUnico" name="validarUnico">
                                                    <label class="form-check-label" for="validarUnico">Valor único</label>
                                                    <div class="form-text">Garante que não há valores duplicados</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mascaraCampo" class="form-label">Máscara</label>
                                                    <input type="text" class="form-control" id="mascaraCampo" name="mascaraCampo" placeholder="Ex: 000.000.000-00">
                                                    <div class="form-text">Defina uma máscara para formatação</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tamanhoMaximo" class="form-label">Tamanho Máximo</label>
                                                    <input type="number" class="form-control" id="tamanhoMaximo" name="tamanhoMaximo" placeholder="Ex: 255">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Campo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="confirmarExclusaoModal" tabindex="-1" aria-labelledby="confirmarExclusaoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmarExclusaoModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o campo <strong id="nomeCampoExclusao"></strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarExclusao">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Inicialização do DataTable
        const tabela = $('#tabelaCampos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                search: "Pesquisar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "Nenhum registro encontrado",
                info: "Mostrando _PAGE_ de _PAGES_",
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
            columnDefs: [
                { orderable: false, targets: [5] } // Desativa ordenação na coluna de ações
            ]
        });

        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione as opções',
            allowClear: true
        });

        // Mostrar/ocultar opções baseado no tipo de campo
        function atualizarVisibilidadeCampos() {
            const tipo = $('#tipoCampo').val();
            const opcoesContainer = $('#opcoesContainer');
            const mascaraCampo = $('#mascaraCampo').closest('.form-group');
            
            // Mostrar/ocultar container de opções
            if (['select', 'radio', 'checkbox'].includes(tipo)) {
                opcoesContainer.slideDown();
            } else {
                opcoesContainer.slideUp();
            }
            
            // Mostrar/ocultar máscara para campos específicos
            if (['cpf', 'cnpj', 'telefone', 'cep', 'nif', 'niss', 'cartaocidadao', 'mod99'].includes(tipo)) {
                let mascara = '';
                switch(tipo) {
                    case 'cpf': mascara = '000.000.000-00'; break;
                    case 'cnpj': mascara = '00.000.000/0000-00'; break;
                    case 'telefone': mascara = '(00) 00000-0000'; break;
                    case 'cep': mascara = '00000-000'; break;
                    case 'nif': mascara = '000 000 000'; break;
                    case 'niss': mascara = '0 00 00 00 000 000 0'; break;
                    case 'cartaocidadao': mascara = '00000000 0 AA0'; break;
                    case 'mod99': 
                        mascara = '';
                        // Configurações específicas para Mod 99
                        $('#requerComprovativo').prop('checked', true).prop('disabled', true);
                        break;
                }
                $('#mascaraCampo').val(mascara);
                mascaraCampo.show();
            } else {
                mascaraCampo.hide();
                $('#requerComprovativo').prop('disabled', false);
            }
            
            // Configurações específicas para campos de arquivo
            if (tipo === 'arquivo') {
                $('#tamanhoMaximo').closest('.form-group').show();
            } else {
                $('#tamanhoMaximo').closest('.form-group').hide();
            }
        }
        
        // Atualizar visibilidade ao carregar a página e ao mudar o tipo
        atualizarVisibilidadeCampos();
        $('#tipoCampo').change(atualizarVisibilidadeCampos);

        // Aplicar máscara ao campo de nome
        $('#nomeCampo').on('input', function() {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });

        // Adicionar nova opção
        $('#adicionarOpcao').click(function() {
            const novaOpcao = `
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor" required>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo" required>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
            `;
            $('#opcoesCampos').append(novaOpcao);
        });

        // Remover opção
        $(document).on('click', '.remover-opcao', function() {
            if ($('#opcoesCampos .row').length > 1) {
                $(this).closest('.row').remove();
            } else {
                // Se for a última opção, limpa os campos em vez de remover
                $(this).closest('.row').find('input').val('');
            }
        });

        // Abrir modal para adicionar novo campo
        $('#btnNovoCampo').click(function() {
            // Limpar o formulário
            $('#formCampo')[0].reset();
            $('#campoId').val('');
            $('#campoModalLabel').text('Adicionar Novo Campo');
            
            // Resetar selects
            $('.select2').val(null).trigger('change');
            
            // Limpar opções
            $('#opcoesCampos').html(`
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor" required>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo" required>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
            `);
            
            // Mostrar o modal
            $('#campoModal').modal('show');
        });

        // Abrir modal para editar campo
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            
            // Carregar os dados do campo
            $.ajax({
                url: 'api_campos_personalizados.php?id=' + id,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        const campo = response.dados;
                        
                        // Preencher o formulário
                        $('#campoId').val(campo.id);
                        $('#nomeCampo').val(campo.nome);
                        $('#tipoCampo').val(campo.tipo).trigger('change');
                        $('#rotuloCampo').val(campo.rotulo);
                        $('#placeholderCampo').val(campo.placeholder);
                        $('#valorPadrao').val(campo.valor_padrao);
                        $('#categoriaCampo').val(campo.categoria);
                        $('#ajudaCampo').val(campo.ajuda);
                        
                        // Checkboxes
                        $('#obrigatorio').prop('checked', campo.obrigatorio == 1);
                        $('#ativo').prop('checked', campo.ativo == 1);
                        $('#requerComprovativo').prop('checked', campo.requer_comprovativo == 1);
                        
                        // Preencher selects múltiplos
                        if (campo.visivel_para) {
                            $('select[name="visivel_para[]"]').val(campo.visivel_para).trigger('change');
                        }
                        
                        if (campo.editavel_por) {
                            $('select[name="editavel_por[]"]').val(campo.editavel_por).trigger('change');
                        }
                        
                        // Preencher opções se existirem
                        if (campo.opcoes && Object.keys(campo.opcoes).length > 0) {
                            $('#opcoesCampos').empty();
                            
                            for (const [valor, rotulo] of Object.entries(campo.opcoes)) {
                                const opcao = `
                                    <div class="row g-2 mb-2">
                                        <div class="col-5">
                                            <input type="text" class="form-control" name="opcao_valor[]" value="${valor}" placeholder="Valor" required>
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control" name="opcao_rotulo[]" value="${rotulo}" placeholder="Rótulo" required>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                `;
                                $('#opcoesCampos').append(opcao);
                            }
                        }
                        
                        // Atualizar título do modal
                        $('#campoModalLabel').text('Editar Campo: ' + campo.rotulo);
                        
                        // Mostrar o modal
                        $('#campoModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.erro || 'Erro ao carregar os dados do campo.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao carregar os dados do campo.'
                    });
                }
            });
        });

        // Excluir campo
        $(document).on('click', '.btn-excluir', function() {
            const id = $(this).data('id');
            const nome = $(this).data('nome');
            
            Swal.fire({
                title: 'Tem certeza?',
                text: `Você está prestes a excluir o campo "${nome}". Esta ação não pode ser desfeita!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api_campos_personalizados.php',
                        method: 'DELETE',
                        data: { id: id },
                        success: function(response) {
                            if (response.sucesso) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: 'Campo excluído com sucesso!',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Recarregar a página
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: response.erro || 'Erro ao excluir o campo.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Erro ao excluir o campo.'
                            });
                        }
                    });
                }
            });
        });

        // Enviar formulário
        $('#formCampo').on('submit', function(e) {
            e.preventDefault();
            
            // Coletar dados do formulário
            const formData = new FormData(this);
            const dados = {};
            
            // Converter FormData para objeto
            formData.forEach((value, key) => {
                // Para campos de array (como selects múltiplos)
                if (key.endsWith('[]')) {
                    const chave = key.replace('[]', '');
                    if (!dados[chave]) {
                        dados[chave] = [];
                    }
                    dados[chave].push(value);
                } else {
                    dados[key] = value;
                }
            });
            
            // Processar opções
            if (['select', 'radio', 'checkbox'].includes(dados.tipoCampo)) {
                const opcoes = {};
                const valores = formData.getAll('opcao_valor[]');
                const rotulos = formData.getAll('opcao_rotulo[]');
                
                valores.forEach((valor, index) => {
                    if (valor && rotulos[index]) {
                        opcoes[valor] = rotulos[index];
                    }
                });
                
                dados.opcoes = opcoes;
            }
            
            // Determinar o método HTTP
            const metodo = $('#campoId').val() ? 'PUT' : 'POST';
            const url = 'api_campos_personalizados.php';
            
            // Se for atualização, adicionar o ID ao objeto de dados
            if (metodo === 'PUT') {
                dados.id = $('#campoId').val();
            }
            
            // Enviar requisição
            $.ajax({
                url: url,
                method: metodo,
                data: JSON.stringify(dados),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.mensagem || 'Operação realizada com sucesso!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Fechar o modal e recarregar a página
                            $('#campoModal').modal('hide');
                            window.location.reload();
                        });
                    } else {
                        let mensagemErro = 'Erro ao processar a solicitação.';
                        
                        if (response.erros && response.erros.length > 0) {
                            mensagemErro = response.erros.join('<br>');
                        } else if (response.erro) {
                            mensagemErro = response.erro;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            html: mensagemErro
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let mensagemErro = 'Erro ao processar a solicitação.';
                    
                    try {
                        const respostaErro = JSON.parse(xhr.responseText);
                        if (respostaErro.erro) {
                            mensagemErro = respostaErro.erro;
                        } else if (respostaErro.erros && respostaErro.erros.length > 0) {
                            mensagemErro = respostaErro.erros.join('<br>');
                        }
                    } catch (e) {
                        console.error('Erro ao processar resposta de erro:', e);
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        html: mensagemErro
                    });
                }
            });
        });

        // Fechar o modal ao clicar no botão Cancelar
        $('.btn-cancelar').click(function() {
            $('#campoModal').modal('hide');
        });

        // Inicializar tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
    </script>
    <script>
        $(document).ready(function() {
            // Inicialização do DataTable foi movida para o início do arquivo

            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione as opções',
                allowClear: true
            });

            // Mostrar/ocultar opções baseado no tipo de campo
            function atualizarVisibilidadeCampos() {
                const tipo = $('#tipoCampo').val();
                const opcoesContainer = $('#opcoesContainer');
                const mascaraCampo = $('#mascaraCampo').closest('.form-group');
                
                // Mostrar/ocultar container de opções
                if (['select', 'radio', 'checkbox'].includes(tipo)) {
                    opcoesContainer.slideDown();
                } else {
                    opcoesContainer.slideUp();
                }
                
                // Mostrar/ocultar máscara para campos específicos
                if (['cpf', 'cnpj', 'telefone', 'cep', 'nif', 'niss', 'cartaocidadao', 'mod99'].includes(tipo)) {
                    let mascara = '';
                    switch(tipo) {
                        case 'cpf': mascara = '000.000.000-00'; break;
                        case 'cnpj': mascara = '00.000.000/0000-00'; break;
                        case 'telefone': mascara = '(00) 00000-0000'; break;
                        case 'cep': mascara = '00000-000'; break;
                        case 'nif': mascara = '000 000 000'; break;
                        case 'niss': mascera = '0 00 00 00 000 000 0'; break;
                        case 'cartaocidadao': mascara = '00000000 0 AA0'; break;
                        case 'mod99': 
                            mascara = '';
                            // Configurações específicas para Mod 99
                            $('#requerComprovativo').prop('checked', true).prop('disabled', true);
                            break;
                    }
                    $('#mascaraCampo').val(mascara);
                    mascaraCampo.show();
                } else {
                    mascaraCampo.hide();
                    $('#requerComprovativo').prop('disabled', false);
                }
                
                // Configurações específicas para campos de arquivo
                if (tipo === 'arquivo') {
                    $('#tamanhoMaximo').closest('.form-group').show();
                } else {
                    $('#tamanhoMaximo').closest('.form-group').hide();
                }
            }
            
            // Atualizar visibilidade ao carregar a página e ao mudar o tipo
            atualizarVisibilidadeCampos();
            $('#tipoCampo').change(atualizarVisibilidadeCampos);

            // Aplicar máscara ao campo de nome
            $('#nomeCampo').on('input', function() {
                this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            });

            // Adicionar nova opção
            $('#adicionarOpcao').click(function() {
                const novaOpcao = `
                    <div class="row g-2 mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor" required>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo" required>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#opcoesCampos').append(novaOpcao);
            });

            // Remover opção
            $(document).on('click', '.remover-opcao', function() {
                if ($('#opcoesCampos .row').length > 1) {
                    $(this).closest('.row').remove();
                } else {
                    // Se for a última opção, limpa os campos em vez de remover
                    $(this).closest('.row').find('input').val('');
                }
            });

            // Validação do formulário
            $('#formCampo').on('submit', function(e) {
                e.preventDefault();
                
                // Validação básica
                const nomeCampo = $('#nomeCampo').val();
                const tipoCampo = $('#tipoCampo').val();
                const rotuloCampo = $('#rotuloCampo').val();
                
                if (!nomeCampo || !tipoCampo || !rotuloCampo) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return false;
                }
                
                // Validação de opções para campos de seleção
                if (['select', 'radio', 'checkbox'].includes(tipoCampo)) {
                    let opcoesValidas = true;
                    $('input[name="opcao_valor[]"]').each(function() {
                        const valor = $(this).val();
                        const rotulo = $(this).closest('.row').find('input[name="opcao_rotulo[]"]').val();
                        
                        if (!valor || !rotulo) {
                            opcoesValidas = false;
                            return false; // Sai do loop each
                        }
                    });
                    
                    if (!opcoesValidas) {
                        alert('Por favor, preencha todos os campos de opção corretamente.');
                        return false;
                    }
                }
                
                // Se chegou até aqui, pode enviar o formulário
                this.submit();
            });
            
            // Função para aplicar filtros
            function aplicarFiltros() {
                const tipo = $('#tipoFiltroCampo').val().toLowerCase();
                const status = $('#statusFiltroCampo').val().toLowerCase();
                
                table.rows().every(function() {
                    const row = this.node();
                    const rowTipo = $(this.node()).find('td:eq(1)').text().toLowerCase();
                    const rowStatus = $(this.node()).find('td:eq(5) span').text().toLowerCase();
                    let deveMostrar = true;
                    
                    if (tipo && !rowTipo.includes(tipo)) {
                        deveMostrar = false;
                    }
                    
                    if (status && !rowStatus.includes(status)) {
                        deveMostrar = false;
                    }
                    
                    if (deveMostrar) {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            }
            
            // Aplicar filtros ao alterar os selects
            $('#tipoFiltroCampo, #statusFiltroCampo').change(aplicarFiltros);
            
            // Limpar filtros
            $('#btnLimparFiltros').click(function() {
                $('#tipoFiltroCampo').val('').trigger('change');
                $('#statusFiltroCampo').val('').trigger('change');
                table.rows().every(function() {
                    $(this.node()).show();
                });
            });
            
            // Busca personalizada
            $('#buscaCampo').on('keyup', function() {
                tabela.search($(this).val()).draw();
            });
            
            // Funções de exportação
            function exportarParaPDF() {
                // Implementar lógica de exportação para PDF
                Swal.fire({
                    icon: 'info',
                    title: 'Exportar para PDF',
                    text: 'Funcionalidade de exportação para PDF será implementada em breve.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            function exportarParaExcel() {
                // Implementar lógica de exportação para Excel
                Swal.fire({
                    icon: 'info',
                    title: 'Exportar para Excel',
                    text: 'Funcionalidade de exportação para Excel será implementada em breve.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            function exportarParaCSV() {
                // Implementar lógica de exportação para CSV
                Swal.fire({
                    icon: 'info',
                    title: 'Exportar para CSV',
                    text: 'Funcionalidade de exportação para CSV será implementada em breve.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            // Eventos dos botões de exportação
            $('.btn-exportar-pdf').click(exportarParaPDF);
            $('.btn-exportar-excel').click(exportarParaExcel);
            $('.btn-exportar-csv').click(exportarParaCSV);
            
            // Inicialização do DataTable já foi feita no início do arquivo
            
            // Adiciona filtros personalizados
            $('#filtroTipo, #filtroStatus').on('change', function() {
                tabela.draw();
            });

            // Filtro personalizado para o DataTable
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var tipo = $('#filtroTipo').val();
                    var status = $('#filtroStatus').val();
                    var rowTipo = data[1]; // Índice da coluna de tipo
                    var rowStatus = data[5].toLowerCase(); // Índice da coluna de status
                    
                    if (tipo !== '' && tipo !== rowTipo) {
                        return false;
                    }
                    
                    if (status !== '') {
                        if (status === 'ativo' && rowStatus.indexOf('success') === -1) {
                            return false;
                        }
                        if (status === 'inativo' && rowStatus.indexOf('secondary') === -1) {
                            return false;
                        }
                    }
                    
                    return true;
                });
                
                // Botão para limpar filtros
                $('#limparFiltros').on('click', function() {
                    $('#filtroTipo, #filtroStatus').val('').trigger('change');
                    tabela.search('').draw();
                });
                
                // Atualiza o contador de registros
                tabela.on('draw', function() {
                    var info = tabela.page.info();
                    $('.dataTables_info').text('Mostrando ' + (info.start + 1) + ' a ' + 
                        (info.end) + ' de ' + info.recordsDisplay + ' registros');
                });
            });
            
            // Ordenação personalizada para a coluna de status
            $.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col) {
                return this.api().column(col, {order:'index'}).nodes().map(function(td, i) {
                    return $('input', td).prop('checked') ? '1' : '0';
                });
            };
            
            // Inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Inicializar popovers
            $('[data-bs-toggle="popover"]').popover({
                trigger: 'hover',
                html: true
            });
            
            // Abrir modal para adicionar novo campo
            $('.btn-novo-campo').click(function() {
                resetarFormulario();
                $('#campoModalLabel').text('Adicionar Novo Campo');
                $('#campoModal').modal('show');
            });
            
            // Abrir modal para editar campo
            $(document).on('click', '.btn-editar', function() {
                const id = $(this).data('id');
                // Simulação de dados - substitua por uma chamada AJAX real
                const campo = {
                    id: id,
                    nome: 'telefone_emergencia',
                    tipo: 'texto',
                    rotulo: 'Telefone de Emergência',
                    categoria: 'contato',
                    placeholder: 'Ex: (00) 00000-0000',
                    valor_padrao: '',
                    obrigatorio: true,
                    ativo: true,
                    requer_comprovativo: false,
                    visivel_para: ['admin', 'rh', 'colaborador'],
                    editavel_por: ['admin', 'rh'],
                    ajuda: 'Informe um telefone para contato em caso de emergência',
                    opcoes: {}
                };
                
                preencherFormulario(campo);
                $('#campoModalLabel').text('Editar Campo');
                $('#campoModal').modal('show');
            });
            
            // Abrir modal de confirmação de exclusão
            $(document).on('click', '.btn-excluir', function() {
                const id = $(this).data('id');
                const nome = $(this).data('nome');
                $('#nomeCampoExclusao').text(nome);
                $('#confirmarExclusao').data('id', id);
                $('#confirmarExclusaoModal').modal('show');
            });
            
            // Confirmar exclusão
            $('#confirmarExclusao').click(function() {
                const id = $(this).data('id');
                // Simulação de exclusão - substitua por uma chamada AJAX real
                console.log('Excluindo campo ID:', id);
                
                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Campo excluído com sucesso!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Fechar modal e atualizar tabela
                $('#confirmarExclusaoModal').modal('hide');
                table.ajax.reload();
            });
            
            // Função para preencher o formulário com os dados do campo
            function preencherFormulario(campo) {
                $('#campoId').val(campo.id);
                $('#nomeCampo').val(campo.nome).prop('disabled', true); // Não permitir editar o nome
                $('#tipoCampo').val(campo.tipo).trigger('change');
                $('#rotuloCampo').val(campo.rotulo);
                $('#categoriaCampo').val(campo.categoria);
                $('#placeholderCampo').val(campo.placeholder);
                $('#valorPadrao').val(campo.valor_padrao);
                $('#campoObrigatorio').prop('checked', campo.obrigatorio);
                $('#campoAtivo').prop('checked', campo.ativo);
                $('#requerComprovativo').prop('checked', campo.requer_comprovativo);
                $('#ajudaCampo').val(campo.ajuda);
                
                // Preencher permissões
                $('select[name="visivelPara[]"]').val(campo.visivel_para).trigger('change');
                $('select[name="editavelPor[]"]').val(campo.editavel_por).trigger('change');
                
                // Preencher opções se for um campo de seleção
                if (Object.keys(campo.opcoes).length > 0) {
                    $('#opcoesCampos').empty();
                    $.each(campo.opcoes, function(valor, rotulo) {
                        const novaOpcao = `
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor" value="${valor}" required>
                                </div>
                                <div class="col-5">
                                    <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo" value="${rotulo}" required>
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        $('#opcoesCampos').append(novaOpcao);
                    });
                }
            }
            
            // Função para resetar o formulário
            function resetarFormulario() {
                $('#formCampo')[0].reset();
                $('#campoId').val('');
                $('.select2').val(null).trigger('change');
                $('#opcoesCampos').html(`
                    <div class="row g-2 mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="opcao_valor[]" placeholder="Valor" required>
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="opcao_rotulo[]" placeholder="Rótulo" required>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-outline-danger w-100 remover-opcao" type="button">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `);
                $('input[type="checkbox"]').prop('checked', false);
                $('#campoAtivo').prop('checked', true);
                $('#nomeCampo').prop('disabled', false);
            }
            
            // Enviar formulário
            $('#formCampo').on('submit', function(e) {
                e.preventDefault();
                
                // Validação básica
                const nomeCampo = $('#nomeCampo').val();
                const tipoCampo = $('#tipoCampo').val();
                const rotuloCampo = $('#rotuloCampo').val();
                
                if (!nomeCampo || !tipoCampo || !rotuloCampo) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Por favor, preencha todos os campos obrigatórios.'
                    });
                    return false;
                }
                
                // Simulação de salvamento - substitua por uma chamada AJAX real
                const formData = new FormData(this);
                const dados = Object.fromEntries(formData.entries());
                console.log('Dados do formulário:', dados);
                
                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Campo salvo com sucesso!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Fechar modal e atualizar tabela
                $('#campoModal').modal('hide');
                tabela.ajax.reload();
            });
            
            // Fechar modal ao clicar no botão de fechar
            $('.modal').on('hidden.bs.modal', function() {
                resetarFormulario();
            });
        });
    </script>
</body>
</html>
