<?php
require_once __DIR__ . '/../../BLL/ColaboradorBLL.php';
require_once __DIR__ . '/../../BLL/LoginBLL.php';

// Verificar autenticação
$loginBLL = new LoginBLL();
if (!$loginBLL->verificarAutenticacao() || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

$colaboradorBLL = new ColaboradorBLL();

// Processar filtros
$filtros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nome'])) {
        $filtros['nome'] = $_POST['nome'];
    }
    if (!empty($_POST['estado'])) {
        $filtros['estado'] = $_POST['estado'];
    }
    
    // Exportar para Excel
    if (isset($_POST['exportar_excel'])) {
        $dados = $colaboradorBLL->exportarParaExcel($filtros);
        
        // Gerar arquivo Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="colaboradores_exportados.xlsx"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $dados['cabecalhos'], ';');
        
        foreach ($dados['dados'] as $linha) {
            fputcsv($output, $linha, ';');
        }
        
        fclose($output);
        exit;
    }
}

// Obter lista de colaboradores
$colaboradores = $colaboradorBLL->obterTodos($filtros);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colaboradores - Painel RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: #fff;
            padding: 20px 0;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            margin: 5px 0;
            border-radius: 5px;
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        .table th {
            border-top: none;
            font-weight: 600;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h4>Painel RH</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class='bx bxs-dashboard'></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="colaboradores.php" class="nav-link active">
                            <i class='bx bxs-user-detail'></i> Colaboradores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="documentos.php" class="nav-link">
                            <i class='bx bxs-file-doc'></i> Documentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="relatorios.php" class="nav-link">
                            <i class='bx bxs-report'></i> Relatórios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="configuracoes.php" class="nav-link">
                            <i class='bx bxs-cog'></i> Configurações
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a href="../logout.php" class="nav-link text-danger">
                            <i class='bx bx-log-out'></i> Sair
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gerenciar Colaboradores</h2>
                    <div>
                        <span class="me-2">Bem-vindo(a), <?php echo $_SESSION['usuario_nome'] ?? 'Usuário'; ?></span>
                    </div>
                </div>

                <!-- Filtros e Ações -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class='bx bx-filter-alt'></i> Filtros</span>
                        <a href="adicionar_colaborador.php" class="btn btn-primary btn-sm">
                            <i class='bx bx-plus'></i> Novo Colaborador
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <div class="col-md-5">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo $_POST['nome'] ?? ''; ?>" placeholder="Pesquisar por nome...">
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos os Estados</option>
                                    <option value="Ativo" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                                    <option value="Inativo" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                                    <option value="Licença" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Licença') ? 'selected' : ''; ?>>Licença</option>
                                    <option value="Férias" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Férias') ? 'selected' : ''; ?>>Férias</option>
                                    <option value="Baixa Médica" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'Baixa Médica') ? 'selected' : ''; ?>>Baixa Médica</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class='bx bx-search'></i> Filtrar
                                </button>
                                <button type="submit" name="exportar_excel" class="btn btn-success">
                                    <i class='bx bx-export'></i> Exportar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Colaboradores -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class='bx bx-list-ul'></i> Lista de Colaboradores</span>
                        <span class="badge bg-primary"><?php echo count($colaboradores); ?> registros</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Colaborador</th>
                                        <th>Contactos</th>
                                        <th>Cargo</th>
                                        <th>Data Entrada</th>
                                        <th>Estado</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($colaboradores)): ?>
                                        <?php foreach ($colaboradores as $colab): ?>
                                            <tr>
                                                <td><?php echo $colab['id_colaborador']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo !empty($colab['foto']) ? $colab['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($colab['nome']) . '&background=random'; ?>" 
                                                             class="avatar me-3" alt="<?php echo htmlspecialchars($colab['nome']); ?>">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($colab['nome']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($colab['email']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><i class='bx bx-phone me-2'></i> <?php echo $colab['telefone'] ?? 'N/D'; ?></div>
                                                    <div><i class='bx bx-mobile me-2'></i> <?php echo $colab['telemovel'] ?? 'N/D'; ?></div>
                                                </td>
                                                <td><?php echo $colab['funcao_nome'] ?? 'N/D'; ?></td>
                                                <td><?php echo !empty($colab['data_entrada']) ? date('d/m/Y', strtotime($colab['data_entrada'])) : 'N/D'; ?></td>
                                                <td>
                                                    <?php 
                                                        $badgeClass = 'bg-secondary';
                                                        if ($colab['estado'] == 'Ativo') $badgeClass = 'bg-success';
                                                        if ($colab['estado'] == 'Inativo') $badgeClass = 'bg-danger';
                                                        if ($colab['estado'] == 'Licença' || $colab['estado'] == 'Férias') $badgeClass = 'bg-warning text-dark';
                                                    ?>
                                                    <span class="status-badge <?php echo $badgeClass; ?>">
                                                        <?php echo $colab['estado']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="visualizar_colaborador.php?id=<?php echo $colab['id_colaborador']; ?>" 
                                                       class="btn btn-sm btn-outline-info btn-action" title="Visualizar">
                                                        <i class='bx bx-show'></i>
                                                    </a>
                                                    <a href="editar_colaborador.php?id=<?php echo $colab['id_colaborador']; ?>" 
                                                       class="btn btn-sm btn-outline-primary btn-action" title="Editar">
                                                        <i class='bx bx-edit'></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                                            title="Excluir" onclick="confirmarExclusao(<?php echo $colab['id_colaborador']; ?>, '<?php echo htmlspecialchars(addslashes($colab['nome'])); ?>')">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class='bx bx-info-circle' style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                                    <p class="mb-0">Nenhum colaborador encontrado com os filtros atuais</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <?php if (count($colaboradores) > 0): ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarExclusao(id, nome) {
            Swal.fire({
                title: 'Confirmar Exclusão',
                text: 'Tem certeza que deseja excluir o colaborador ' + nome + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aqui você pode adicionar a lógica para excluir o colaborador
                    // Exemplo com fetch API:
                    fetch('excluir_colaborador.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Excluído!',
                                'O colaborador foi excluído com sucesso.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Erro!',
                                data.message || 'Ocorreu um erro ao excluir o colaborador.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Atualizar a cada 5 minutos
        setTimeout(function() {
            window.location.reload();
        }, 5 * 60 * 1000);
    </script>
</body>
</html>
