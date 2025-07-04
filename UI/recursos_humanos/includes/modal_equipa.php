<?php
// Verificar se o usuário tem permissão para gerenciar equipes
$podeGerenciarEquipas = isset($_SESSION['id_perfilacesso']) && in_array($_SESSION['id_perfilacesso'], [1, 2]);

if (!$podeGerenciarEquipas) {
    return;
}
?>

<!-- Modal Equipe -->
<div class="modal fade" id="modalEquipa" tabindex="-1" aria-labelledby="modalEquipaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEquipaLabel">Nova Equipe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="formContainer">
                <form id="formEquipa">
                    <input type="hidden" id="equipaId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nome" class="form-label">Nome da Equipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="coordenador_id" class="form-label">Coordenador <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="coordenador_id" name="coordenador_id" required>
                                <option value="">Selecione um coordenador</option>
                                <?php
                                // Carregar lista de coordenadores
                                try {
                                    $utilizadorBLL = new UtilizadorBLL();
                                    $coordenadores = $utilizadorBLL->obterCoordenadores();
                                    
                                    foreach ($coordenadores as $coordenador) {
                                        echo "<option value=\"$coordenador[id_utilizador]\">$coordenador[nome]</option>";
                                    }
                                } catch (Exception $e) {
                                    error_log('Erro ao carregar coordenadores: ' . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="id_departamento" class="form-label">Departamento</label>
                            <select class="form-select select2" id="id_departamento" name="id_departamento">
                                <option value="">Selecione um departamento</option>
                                <?php
                                // Carregar lista de departamentos
                                try {
                                    $departamentoBLL = new DepartamentoBLL();
                                    $departamentos = $departamentoBLL->listarDepartamentos();
                                    
                                    foreach ($departamentos as $departamento) {
                                        echo "<option value=\"$departamento[id_departamento]\">$departamento[nome]</option>";
                                    }
                                } catch (Exception $e) {
                                    error_log('Erro ao carregar departamentos: ' . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_equipa_pai" class="form-label">Equipe Pai</label>
                            <select class="form-select select2" id="id_equipa_pai" name="id_equipa_pai">
                                <option value="">Nenhuma (Equipe Principal)</option>
                                <?php
                                // Carregar lista de equipes
                                try {
                                    $equipaBLL = new EquipaBLL();
                                    $equipas = $equipaBLL->listarEquipas();
                                    
                                    foreach ($equipas as $equipa) {
                                        echo "<option value=\"$equipa[id_equipa]\">$equipa[nome]</option>";
                                    }
                                } catch (Exception $e) {
                                    error_log('Erro ao carregar equipes: ' . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" checked>
                                <label class="form-check-label" for="ativo">Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Membros da Equipe</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" id="buscarMembro" placeholder="Buscar membros...">
                        </div>
                        
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            <div id="listaMembros">
                                <?php
                                // Carregar lista de funcionários
                                try {
                                    $utilizadorBLL = new UtilizadorBLL();
                                    $funcionarios = $utilizadorBLL->listarFuncionariosAtivos();
                                    
                                    foreach ($funcionarios as $funcionario) {
                                        echo "
                                        <div class='form-check mb-2'>
                                            <input class='form-check-input membro-checkbox' type='checkbox' 
                                                   name='membros[]' value='$funcionario[id_utilizador]' id='membro_$funcionario[id_utilizador]'>
                                            <label class='form-check-label' for='membro_$funcionario[id_utilizador]'>
                                                $funcionario[nome] <small class='text-muted'>($funcionario[email])</small>
                                            </label>
                                        </div>";
                                    }
                                } catch (Exception $e) {
                                    error_log('Erro ao carregar funcionários: ' . $e->getMessage());
                                    echo "<div class='text-muted'>Erro ao carregar a lista de funcionários.</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSalvar">
                            <i class="bx bx-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Filtro de busca de membros
$(document).ready(function() {
    $('#buscarMembro').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        
        $('#listaMembros .form-check').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchText));
        });
    });
});
</script>