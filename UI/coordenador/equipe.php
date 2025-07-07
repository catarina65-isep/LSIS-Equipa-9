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

// Define a página atual para destacar no menu
$pagina_atual = 'equipe';

// Define o título da página
$page_title = "Minha Equipe - Coordenador - Tlantic";

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
                <h1 class="h2">Minha Equipe</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-4">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" placeholder="Nome, matrícula ou cargo">
                        </div>
                        <div class="col-md-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <select id="cargo" class="form-select">
                                <option selected>Todos</option>
                                <option>Desenvolvedor</option>
                                <option>Designer</option>
                                <option>Gerente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-select">
                                <option selected>Todos</option>
                                <option>Ativo</option>
                                <option>Férias</option>
                                <option>Afastado</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Membros da Equipe -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-3" alt="Avatar">
                                            <div>
                                                <h6 class="mb-0">João Silva</h6>
                                                <small class="text-muted">#EMP001</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Desenvolvedor Sênior</td>
                                    <td>joao.silva@empresa.com</td>
                                    <td>(11) 98765-4321</td>
                                    <td><span class="badge bg-success">Ativo</span></td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="ver_colaborador.php?id=1" class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-3" alt="Avatar">
                                            <div>
                                                <h6 class="mb-0">Maria Santos</h6>
                                                <small class="text-muted">#EMP002</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Designer UX/UI</td>
                                    <td>maria.santos@empresa.com</td>
                                    <td>(11) 98765-1234</td>
                                    <td><span class="badge bg-warning">Férias</span></td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="ver_colaborador.php?id=2" class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-3" alt="Avatar">
                                            <div>
                                                <h6 class="mb-0">Carlos Oliveira</h6>
                                                <small class="text-muted">#EMP003</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Analista de Sistemas</td>
                                    <td>carlos.oliveira@empresa.com</td>
                                    <td>(11) 98765-5678</td>
                                    <td><span class="badge bg-success">Ativo</span></td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="ver_colaborador.php?id=3" class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <nav aria-label="Navegação de páginas" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Inclui o rodapé
include_once __DIR__ . '/../includes/footer.php';
?>
