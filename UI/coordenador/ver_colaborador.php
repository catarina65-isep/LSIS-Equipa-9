<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é um coordenador
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 3) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Verifica se foi fornecido um ID de colaborador
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: equipe.php');
    exit;
}

$colaborador_id = (int)$_GET['id'];

// Aqui você buscaria os dados do colaborador do banco de dados
// Por enquanto, vamos simular dados
$colaborador = [
    'id' => $colaborador_id,
    'nome' => 'João Silva',
    'matricula' => 'EMP001',
    'cargo' => 'Desenvolvedor Sênior',
    'email' => 'joao.silva@empresa.com',
    'telefone' => '(11) 98765-4321',
    'data_admissao' => '15/03/2020',
    'status' => 'Ativo',
    'foto' => 'https://via.placeholder.com/150',
    'equipe' => 'Desenvolvimento',
    'gestor' => 'Maria Oliveira',
    'data_nascimento' => '15/05/1990',
    'cpf' => '123.456.789-00',
    'rg' => '12.345.678-9',
    'endereco' => 'Rua Exemplo, 123',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01234-567'
];

// Define a página atual para destacar no menu
$pagina_atual = 'equipe';

// Define o título da página
$page_title = "Perfil do Colaborador - " . $colaborador['nome'] . " - Tlantic";

// Inclui o cabeçalho
include_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        
        <!-- Conteúdo principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="equipe.php">Minha Equipe</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($colaborador['nome']) ?></li>
                    </ol>
                </nav>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="equipe.php" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Coluna da esquerda - Foto e informações básicas -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="<?= htmlspecialchars($colaborador['foto']) ?>" class="rounded-circle mb-3" alt="Foto de <?= htmlspecialchars($colaborador['nome']) ?>" style="width: 150px; height: 150px; object-fit: cover;">
                            <h4 class="card-title mb-1"><?= htmlspecialchars($colaborador['nome']) ?></h4>
                            <p class="text-muted mb-3"><?= htmlspecialchars($colaborador['cargo']) ?></p>
                            
                            <div class="d-flex justify-content-center mb-3">
                                <span class="badge bg-<?= $colaborador['status'] == 'Ativo' ? 'success' : 'warning' ?> fs-6">
                                    <?= htmlspecialchars($colaborador['status']) ?>
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary">
                                    <i class="bi bi-envelope me-2"></i> Enviar Mensagem
                                </button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <h6 class="mb-0">Equipe</h6>
                                    <small class="text-muted"><?= htmlspecialchars($colaborador['equipe']) ?></small>
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-0">Gestor</h6>
                                    <small class="text-muted"><?= htmlspecialchars($colaborador['gestor']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coluna da direita - Informações detalhadas -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="perfilTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="dados" aria-selected="true">
                                        Dados Pessoais
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contato-tab" data-bs-toggle="tab" data-bs-target="#contato" type="button" role="tab" aria-controls="contato" aria-selected="false">
                                        Contato
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false">
                                        Documentos
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="perfilTabContent">
                                <!-- Aba Dados Pessoais -->
                                <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nome Completo</strong></p>
                                            <p><?= htmlspecialchars($colaborador['nome']) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Data de Nascimento</strong></p>
                                            <p><?= htmlspecialchars($colaborador['data_nascimento']) ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>CPF</strong></p>
                                            <p><?= htmlspecialchars($colaborador['cpf']) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>RG</strong></p>
                                            <p><?= htmlspecialchars($colaborador['rg']) ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Data de Admissão</strong></p>
                                            <p><?= htmlspecialchars($colaborador['data_admissao']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Contato -->
                                <div class="tab-pane fade" id="contato" role="tabpanel" aria-labelledby="contato-tab">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>E-mail</strong></p>
                                            <p><?= htmlspecialchars($colaborador['email']) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Telefone</strong></p>
                                            <p><?= htmlspecialchars($colaborador['telefone']) ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Endereço</strong></p>
                                            <p>
                                                <?= htmlspecialchars($colaborador['endereco']) ?><br>
                                                <?= htmlspecialchars($colaborador['cidade']) ?> - <?= htmlspecialchars($colaborador['estado']) ?><br>
                                                CEP: <?= htmlspecialchars($colaborador['cep']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Documentos -->
                                <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Documento</th>
                                                    <th>Data de Upload</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Contrato de Trabalho</td>
                                                    <td>15/03/2020</td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-download"></i> Baixar
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Carteira de Trabalho</td>
                                                    <td>16/03/2020</td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-download"></i> Baixar
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Comprovante de Residência</td>
                                                    <td>17/03/2020</td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-download"></i> Baixar
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Inclui o rodapé
include_once __DIR__ . '/../includes/footer.php';
?>
