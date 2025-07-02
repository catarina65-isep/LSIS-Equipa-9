<?php
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../includes/verificar_acesso.php';

if (!isset($_GET['id'])) {
    header('Location: equipas.php');
    exit();
}

$equipaBLL = new EquipaBLL();
$equipa = $equipaBLL->obterEquipa($_GET['id']);

if (!$equipa) {
    header('Location: equipas.php?erro=equipa_nao_encontrada');
    exit();
}

$tituloPagina = 'Detalhes da Equipa: ' . htmlspecialchars($equipa['nome']);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloPagina; ?> - Painel de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            font-weight: 600;
        }
        .membro-card {
            transition: transform 0.2s;
        }
        .membro-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <a href="equipas.php" class="text-decoration-none text-dark">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <?php echo htmlspecialchars($equipa['nome']); ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary me-2" 
                                onclick="editarEquipa(<?php echo htmlspecialchars(json_encode($equipa)); ?>)">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <form method="POST" action="equipas.php" class="d-inline" 
                              onsubmit="return confirm('Tem certeza que deseja excluir esta equipa?');">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?php echo $equipa['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <!-- Informações da Equipa -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Informações da Equipa
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($equipa['nome']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($equipa['descricao'])); ?></p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> 
                                        Criada em: <?php echo date('d/m/Y H:i', strtotime($equipa['data_criacao'])); ?>
                                    </small>
                                </p>
                            </div>
                        </div>

                        <!-- Coordenador -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-person-badge"></i> Coordenador
                            </div>
                            <div class="card-body">
                                <?php if (isset($equipa['coordenador_username'])): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; font-size: 1.25rem;">
                                                <?php echo strtoupper(substr($equipa['coordenador_username'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($equipa['coordenador_username']); ?></h5>
                                            <p class="mb-0 text-muted">Coordenador</p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Nenhum coordenador definido.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Membros da Equipa -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-people"></i> Membros (<?php echo count($equipa['membros']); ?>)</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarMembro(<?php echo $equipa['id']; ?>)">
                                    <i class="bi bi-plus"></i> Adicionar
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <?php if (!empty($equipa['membros'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($equipa['membros'] as $membro): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                        <?php echo strtoupper(substr($membro['nome'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($membro['nome']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($membro['email'] ?? ''); ?></small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="removerMembro(<?php echo $equipa['id']; ?>, <?php echo $membro['id']; ?>, '<?php echo addslashes($membro['nome']); ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center p-4">
                                        <i class="bi bi-people-slash" style="font-size: 2rem; color: #6c757d;"></i>
                                        <p class="mt-2 mb-0">Nenhum membro na equipa</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Adicionar Membro -->
    <div class="modal fade" id="adicionarMembroModal" tabindex="-1" aria-labelledby="adicionarMembroModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarMembroModalLabel">Adicionar Membro à Equipa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="listaUsuarios">
                        <!-- Lista de usuários será preenchida via AJAX -->
                        <div class="text-center my-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando usuários...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnAdicionarMembros" disabled>Adicionar Selecionados</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let equipaAtualId = <?php echo $equipa['id']; ?>;
        
        function editarEquipa(equipa) {
            window.location.href = `equipas.php?editar=${equipa.id}`;
        }
        
        function adicionarMembro(equipaId) {
            const modal = new bootstrap.Modal(document.getElementById('adicionarMembroModal'));
            equipaAtualId = equipaId;
            
            // Carrega a lista de usuários
            fetch(`api_usuarios.php?acao=listar`)
                .then(response => response.json())
                .then(usuarios => {
                    const membrosAtuais = <?php echo json_encode(array_column($equipa['membros'], 'id')); ?>;
                    const coordenadorId = <?php echo $equipa['coordenador_id'] ?? 0; ?>;
                    
                    // Filtra utilizadores que ainda não estão na equipa e não são o coordenador
                    const usuariosDisponiveis = usuarios.filter(usuario => 
                        !membrosAtuais.includes(parseInt(usuario.id)) && 
                        usuario.id != coordenadorId
                    );
                    
                    const listaUsuarios = document.getElementById('listaUsuarios');
                    
                    if (usuariosDisponiveis.length === 0) {
                        listaUsuarios.innerHTML = `
                            <div class="text-center p-4">
                                <i class="bi bi-emoji-frown" style="font-size: 2rem; color: #6c757d;"></i>
                                <p class="mt-2">Todos os utilizadores já estão nesta equipa ou não há utilizadores disponíveis.</p>
                            </div>
                        `;
                        document.getElementById('btnAdicionarMembros').disabled = true;
                        return;
                    }
                    
                    let html = '<div class="list-group">';
                    usuariosDisponiveis.forEach(usuario => {
                        html += `
                            <label class="list-group-item d-flex align-items-center">
                                <input class="form-check-input me-3 usuario-checkbox" type="checkbox" value="${usuario.id}">
                                <div>
                                    <div class="fw-bold">${usuario.nome}</div>
                                    <small class="text-muted">${usuario.email || ''}</small>
                                </div>
                            </label>
                        `;
                    });
                    html += '</div>';
                    
                    listaUsuarios.innerHTML = html;
                    
                    // Habilita/desabilita o botão de adicionar com base na seleção
                    const checkboxes = document.querySelectorAll('.usuario-checkbox');
                    const btnAdicionar = document.getElementById('btnAdicionarMembros');
                    
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', () => {
                            const peloMenosUmSelecionado = Array.from(checkboxes).some(cb => cb.checked);
                            btnAdicionar.disabled = !peloMenosUmSelecionado;
                        });
                    });
                    
                    // Configura o botão de adicionar
                    btnAdicionar.onclick = function() {
                        const selecionados = Array.from(document.querySelectorAll('.usuario-checkbox:checked'))
                            .map(cb => cb.value);
                        
                        if (selecionados.length === 0) return;
                        
                        // Envia requisição para adicionar membros
                        fetch('api_equipas.php?acao=adicionar_membros', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                equipa_id: equipaAtualId,
                                membros: selecionados
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.sucesso) {
                                // Recarrega a página para mostrar os novos membros
                                window.location.reload();
                            } else {
                                alert('Erro ao adicionar membros: ' + (data.mensagem || 'Erro desconhecido'));
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao adicionar membros. Por favor, tente novamente.');
                        });
                    };
                })
                .catch(error => {
                    console.error('Erro ao carregar usuários:', error);
                    document.getElementById('listaUsuarios').innerHTML = `
                        <div class="alert alert-danger">
                            Erro ao carregar a lista de usuários. Por favor, tente novamente.
                        </div>
                    `;
                });
            
            modal.show();
        }
        
        function removerMembro(equipaId, membroId, membroNome) {
            if (!confirm(`Tem certeza que deseja remover ${membroNome} da equipa?`)) {
                return;
            }
            
            fetch('api_equipas.php?acao=remover_membro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    equipa_id: equipaId,
                    membro_id: membroId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    // Recarrega a página para refletir as mudanças
                    window.location.reload();
                } else {
                    alert('Erro ao remover membro: ' + (data.mensagem || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao remover membro. Por favor, tente novamente.');
            });
        }
    </script>
</body>
</html>
