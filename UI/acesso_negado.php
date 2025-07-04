<?php
// Define a variável para pular a verificação de acesso
$pular_verificacao = true;

// Inclui o cabeçalho
include_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Acesso Negado</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-5x text-muted mb-4"></i>
                        <h2 class="text-danger">Acesso Restrito</h2>
                        <p class="lead">Você não tem permissão para acessar esta página.</p>
                        
                        <?php if (isset($_SESSION['utilizador_id'])): ?>
                            <p>Seu perfil atual é: <strong><?php echo obterNomePerfil($_SESSION['id_perfilacesso']); ?></strong></p>
                            <div class="mt-4">
                                <a href="/LSIS-Equipa-9/UI/dashboard.php" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Voltar ao Início
                                </a>
                                <a href="/LSIS-Equipa-9/UI/logout.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a>
                            </div>
                        <?php else: ?>
                            <p>Por favor, faça login para continuar.</p>
                            <div class="mt-4">
                                <a href="/LSIS-Equipa-9/UI/login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Se você acredita que isso é um erro, entre em contato com o administrador do sistema.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
include_once 'includes/footer.php';
?>
