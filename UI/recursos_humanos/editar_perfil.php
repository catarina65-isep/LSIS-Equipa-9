<?php
session_start();
require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';
require_once __DIR__ . '/../../BLL/PerfilAcessoBLL.php';

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: ../login.php');
    exit;
}

$utilizadorBLL = new UtilizadorBLL();
$perfilBLL = new PerfilAcessoBLL();

// Obter ID do usuário a ser editado
$usuarioId = $_GET['id'] ?? null;
$usuario = null;

if ($usuarioId) {
    $usuario = $utilizadorBLL->obterPorId($usuarioId);
    if (!$usuario) {
        $_SESSION['erro'] = 'Utilizador não encontrado.';
        header('Location: perfis.php');
        exit;
    }
} else {
    $_SESSION['erro'] = 'ID de utilizador não fornecido.';
    header('Location: perfis.php');
    exit;
}

// Obter lista de perfis de acesso
$perfis = $perfilBLL->listarTodos();

$page_title = 'Editar Perfil - ' . htmlspecialchars($usuario['nome'] ?? $usuario['username']);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
            --danger: #ef476f;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control, .form-select, .select2-selection {
            border-radius: 0.35rem;
            padding: 0.5rem 0.75rem;
            height: calc(1.5em + 0.75rem + 2px);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
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
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Editar Perfil</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                                <li class="breadcrumb-item"><a href="perfis.php">Perfis</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Editar</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="perfis.php" class="btn btn-outline-secondary me-2">
                            <i class='bx bx-arrow-back me-1'></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="container-fluid px-4">
                    <!-- Mensagens de feedback -->
                    <?php if (isset($_SESSION['sucesso'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['sucesso']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                        <?php unset($_SESSION['sucesso']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['erro'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['erro']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                        <?php unset($_SESSION['erro']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($usuario['nome'] ?? $usuario['username']) ?>&background=4361ee&color=fff&size=200" 
                                         alt="Avatar" class="avatar mb-3">
                                    <h5 class="mb-1"><?= htmlspecialchars($usuario['nome'] ?? $usuario['username']) ?></h5>
                                    <p class="text-muted mb-3"><?= htmlspecialchars($usuario['email']) ?></p>
                                    <div class="d-flex justify-content-center mb-2">
                                        <button type="button" class="btn btn-primary">Alterar Foto</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Informações do Perfil</h5>
                                </div>
                                <div class="card-body">
                                    <form id="formEditarPerfil" action="atualizar_perfil.php" method="POST">
                                        <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['id_utilizador']) ?>">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="nome" name="nome" 
                                                       value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" 
                                                       required
                                                       minlength="3"
                                                       maxlength="100"
                                                       data-pristine-required-message="O nome é obrigatório"
                                                       data-pristine-minlength-message="O nome deve ter pelo menos 3 caracteres">
                                                <div class="invalid-feedback">Por favor, insira um nome válido (mínimo 3 caracteres)</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" 
                                                       required
                                                       data-pristine-required-message="O e-mail é obrigatório"
                                                       data-pristine-email-message="Por favor, insira um e-mail válido">
                                                <div class="invalid-feedback">Por favor, insira um e-mail válido</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="username" class="form-label">Nome de Utilizador <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">@</span>
                                                    <input type="text" class="form-control" id="username" name="username" 
                                                           value="<?= htmlspecialchars($usuario['username'] ?? '') ?>" 
                                                           required
                                                           minlength="3"
                                                           maxlength="50"
                                                           pattern="[a-zA-Z0-9_.-]+"
                                                           data-pristine-required-message="O nome de utilizador é obrigatório"
                                                           data-pristine-minlength-message="O nome de utilizador deve ter pelo menos 3 caracteres"
                                                           data-pristine-pattern-message="Use apenas letras, números, pontos, hífens ou underscores">
                                                </div>
                                                <div class="invalid-feedback">
                                                    Por favor, insira um nome de utilizador válido (3-50 caracteres, sem espaços)
                                                </div>
                                                <small class="form-text text-muted">Não utilize espaços ou caracteres especiais</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perfil" class="form-label">Perfil de Acesso <span class="text-danger">*</span></label>
                                                <select class="form-select select2" id="perfil" name="perfil" required
                                                        data-pristine-required-message="Selecione um perfil de acesso">
                                                    <option value="">Selecione um perfil...</option>
                                                    <?php foreach ($perfis as $perfil): ?>
                                                        <option value="<?= $perfil['id_perfilacesso'] ?>" 
                                                            <?= ($perfil['id_perfilacesso'] == $usuario['id_perfilacesso']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($perfil['nome_perfil']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">Por favor, selecione um perfil de acesso</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="password" class="form-label">Nova Senha (opcional)</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password" name="password"
                                                           minlength="6"
                                                           data-pristine-minlength-message="A senha deve ter pelo menos 6 caracteres"
                                                           data-toggle="password">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                                        <i class='bx bx-hide'></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Deixe em branco para manter a senha atual</div>
                                                <div class="password-strength mt-1">
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <small class="password-strength-text text-muted"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                                           data-pristine-equals="#password"
                                                           data-pristine-equals-message="As senhas não coincidem"
                                                           data-toggle="password">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirm_password">
                                                        <i class='bx bx-hide'></i>
                                                    </button>
                                                </div>
                                                <div class="invalid-feedback">As senhas não coincidem</div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check form-switch mb-4">
                                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                                   <?= ($usuario['ativo'] ?? 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="ativo">Conta Ativa</label>
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            <a href="perfis.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class='bx bx-save me-1'></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Validação de senha
            $('#password, #confirm_password').on('keyup', function() {
                const password = $('#password').val();
                const confirm = $('#confirm_password').val();
                
                if (password !== '' && confirm !== '') {
                    if (password === confirm) {
                        $('#confirm_password').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $('#confirm_password').removeClass('is-valid').addClass('is-invalid');
                    }
                } else {
                    $('#confirm_password').removeClass('is-valid is-invalid');
                }
            });

            // Envio do formulário via AJAX
            $('#formEditarPerfil').on('submit', function(e) {
                e.preventDefault();
                
                // Validar senhas
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (password !== '' && password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'As senhas não coincidem!',
                        confirmButtonColor: '#4361ee'
                    });
                    return false;
                }
                
                // Mostrar loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
                
                // Coletar dados do formulário
                const formData = new FormData(this);
                
                // Enviar via AJAX
                $.ajax({
                    url: 'atualizar_perfil.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message || 'Perfil atualizado com sucesso!',
                                confirmButtonColor: '#4361ee'
                            }).then((result) => {
                                // Recarregar a página para atualizar os dados
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Ocorreu um erro ao atualizar o perfil.',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição:', status, error);
                        let errorMessage = 'Ocorreu um erro ao processar a requisição.';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Erro ao processar resposta:', e);
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMessage,
                            confirmButtonColor: '#4361ee'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
        });
    </script>
</body>
</html>
