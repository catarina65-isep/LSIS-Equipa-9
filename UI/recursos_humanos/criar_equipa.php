<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar se o usuário está logado e tem permissão
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header("Location: /LSIS-Equipa-9/UI/login.php");
    exit();
}

try {
    // Incluir arquivos necessários
    $equipaBLLPath = __DIR__ . '/../../BLL/equipaBLL.php';
    $utilizadorBLLPath = __DIR__ . '/../../BLL/UtilizadorBLL.php';
    
    if (!file_exists($equipaBLLPath) || !file_exists($utilizadorBLLPath)) {
        throw new Exception("Arquivo necessário não encontrado: " . 
                         (!file_exists($equipaBLLPath) ? 'equipaBLL.php' : '') . 
                         (!file_exists($utilizadorBLLPath) ? ' UtilizadorBLL.php' : ''));
    }
    
    require_once $equipaBLLPath;
    require_once $utilizadorBLLPath;

    $equipaBLL = new EquipaBLL();
    $utilizadorBLL = new UtilizadorBLL();

    // Buscar coordenadores e colaboradores disponíveis
    $coordenadores = $utilizadorBLL->obterCoordenadores();
    $colaboradores = $utilizadorBLL->obterFuncionarios();
    
    if ($coordenadores === false) {
        throw new Exception("Erro ao carregar coordenadores: " . print_r(error_get_last(), true));
    }
    
    if ($colaboradores === false) {
        throw new Exception("Erro ao carregar colaboradores: " . print_r(error_get_last(), true));
    }

    $erro = '';
    $sucesso = '';
} catch (Exception $e) {
    // Registrar o erro no log
    error_log("Erro em criar_equipa.php: " . $e->getMessage());
    
    // Exibir mensagem de erro detalhada apenas em desenvolvimento
    if (ini_get('display_errors')) {
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
                <h3>Erro ao carregar a página</h3>
                <p><strong>Mensagem:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                <p><strong>Arquivo:</strong> " . htmlspecialchars($e->getFile()) . " na linha " . $e->getLine() . "</p>
                <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
              </div>");
    } else {
        // Em produção, exibir uma mensagem genérica
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
                Ocorreu um erro ao carregar a página. Por favor, tente novamente mais tarde.
              </div>");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $coordenador_id = !empty($_POST['coordenador_id']) ? (int)$_POST['coordenador_id'] : null;
    $membros = isset($_POST['membros']) ? (array)$_POST['membros'] : [];
    
    if (empty($nome)) {
        $erro = "O nome da equipa é obrigatório.";
    } elseif (empty($coordenador_id)) {
        $erro = "O coordenador da equipa é obrigatório.";
    } else {
        try {
            // Criar a equipa
            $dadosEquipa = [
                'nome' => $nome,
                'descricao' => $descricao,
                'coordenador_id' => $coordenador_id
            ];
            
            $equipa_id = $equipaBLL->criarEquipa($dadosEquipa);
            
            if ($equipa_id) {
                // Adicionar membros à equipa
                if (!empty($membros)) {
                    foreach ($membros as $membro_id) {
                        if ($membro_id != $coordenador_id) { // Não adicionar o coordenador novamente
                            $equipaBLL->adicionarMembro($equipa_id, $membro_id);
                        }
                    }
                }
                
                $_SESSION['mensagem'] = "Equipa criada com sucesso!";
                $_SESSION['tipo_mensagem'] = "success";
                header("Location: equipas.php");
                exit();
            } else {
                $erro = "Ocorreu um erro ao criar a equipa. Tente novamente.";
            }
        } catch (Exception $e) {
            $erro = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Equipa - RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../../CSS/estilo.css" rel="stylesheet">
    <style>
        /* Ajuste para o wrapper principal */
        .wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }
        
        /* Ajuste para o conteúdo principal */
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px; /* Largura da sidebar */
            position: relative;
            z-index: 1;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Ajuste para o container do formulário */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }
        .form-header {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            padding: 20px;
            margin-bottom: 30px;
        }
        .form-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .form-body {
            padding: 0 30px 30px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .form-control, .form-select, .form-check-input {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a0ca3 0%, #4361ee 100%);
        }
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }
        .membros-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .membro-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        .membro-item:last-child {
            border-bottom: none;
        }
        .membro-item:hover {
            background-color: #f1f3ff;
            border-radius: 6px;
        }
        .membro-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4361ee;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: bold;
        }
        .membro-info {
            flex: 1;
        }
        .membro-nome {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .membro-cargo {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .search-box input {
            padding-left: 40px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid py-4">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="equipas.php">Equipas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Nova Equipa</li>
                    </ol>
                </nav>

                <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $erro; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-header">
                        <h2><i class='bx bx-group me-2'></i>Nova Equipa</h2>
                    </div>
                    
                    <div class="form-body">
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="nome" class="form-label">Nome da Equipa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required 
                                       value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" 
                                          rows="3"><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="coordenador_id" class="form-label">Coordenador <span class="text-danger">*</span></label>
                                <select class="form-select" id="coordenador_id" name="coordenador_id" required>
                                    <option value="">Selecione um coordenador</option>
                                    <?php foreach ($coordenadores as $coordenador): ?>
                                        <option value="<?php echo $coordenador['id_utilizador'] ?? $coordenador['id']; ?>"
                                            <?php echo (isset($_POST['coordenador_id']) && $_POST['coordenador_id'] == ($coordenador['id_utilizador'] ?? $coordenador['id'])) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($coordenador['nome'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Membros da Equipa</label>
                                <div class="search-box mb-3">
                                    <i class='bx bx-search'></i>
                                    <input type="text" class="form-control" id="searchMembro" placeholder="Pesquisar membros...">
                                </div>
                                
                                <div class="membros-container">
                                    <?php if (!empty($colaboradores)): ?>
                                        <?php foreach ($colaboradores as $colaborador): 
                                            $colaboradorId = $colaborador['id_utilizador'] ?? $colaborador['id'];
                                            $colaboradorNome = $colaborador['nome'] ?? '';
                                            $isChecked = isset($_POST['membros']) && in_array($colaboradorId, $_POST['membros']) ? 'checked' : '';
                                        ?>
                                            <div class="membro-item">
                                                <div class="form-check">
                                                    <input class="form-check-input membro-checkbox" type="checkbox" 
                                                           name="membros[]" value="<?php echo $colaboradorId; ?>"
                                                           id="membro_<?php echo $colaboradorId; ?>"
                                                           <?php echo $isChecked; ?>>
                                                    <label class="form-check-label" for="membro_<?php echo $colaboradorId; ?>">
                                                        <div class="membro-avatar me-2">
                                                            <?php echo strtoupper(substr($colaboradorNome, 0, 1)); ?>
                                                        </div>
                                                        <div class="membro-info">
                                                            <div class="membro-nome"><?php echo htmlspecialchars($colaboradorNome); ?></div>
                                                            <div class="membro-cargo"><?php echo htmlspecialchars($colaborador['cargo'] ?? 'Sem cargo'); ?></div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            Nenhum colaborador disponível para adicionar à equipe.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="equipas.php" class="btn btn-outline-secondary">
                                    <i class='bx bx-arrow-back me-1'></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save me-1'></i> Salvar Equipa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Garantir que a página seja rolada para o topo ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            window.scrollTo(0, 0);
            
            // Ajustar a altura do conteúdo principal
            const adjustMainContent = () => {
                const headerHeight = document.querySelector('.form-header')?.offsetHeight || 0;
                const windowHeight = window.innerHeight;
                const mainContent = document.querySelector('.main-content');
                
                if (mainContent) {
                    mainContent.style.minHeight = `${windowHeight}px`;
                }
            };
            
            // Ajustar no carregamento e no redimensionamento
            adjustMainContent();
            window.addEventListener('resize', adjustMainContent);

            // Filtro de pesquisa de membros
            const searchInput = document.getElementById('searchMembro');
            const membros = document.querySelectorAll('.membro-item');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    membros.forEach(function(membro) {
                        const nome = membro.textContent.toLowerCase();
                        if (nome.includes(searchTerm)) {
                            membro.style.display = 'flex';
                        } else {
                            membro.style.display = 'none';
                        }
                    });
                });
            }
            
            // Desmarcar o checkbox do coordenador se selecionado como coordenador
            const coordenadorSelect = document.getElementById('coordenador_id');
            if (coordenadorSelect) {
                coordenadorSelect.addEventListener('change', function() {
                    const coordenadorId = this.value;
                    if (coordenadorId) {
                        // Desmarcar o checkbox do coordenador selecionado
                        const coordenadorCheckbox = document.querySelector(`.membro-checkbox[value="${coordenadorId}"]`);
                        if (coordenadorCheckbox) {
                            coordenadorCheckbox.checked = false;
                        }
                        
                        // Atualizar todos os checkboxes
                        document.querySelectorAll('.membro-checkbox').forEach(checkbox => {
                            if (checkbox.value === coordenadorId) {
                                checkbox.disabled = true;
                            } else {
                                checkbox.disabled = false;
                            }
                        });
                    }
                });
                
                // Executar a validação inicial
                if (coordenadorSelect.value) {
                    coordenadorSelect.dispatchEvent(new Event('change'));
                }
            }
        });
        }); // Fechamento do DOMContentLoaded
    </script>
</body>
</html>
