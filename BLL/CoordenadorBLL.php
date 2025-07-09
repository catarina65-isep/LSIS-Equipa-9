<?php
require_once __DIR__ . '/../DAL/CoordenadorDAL.php';

class CoordenadorBLL {
    private $coordenadorDAL;
    
    public function __construct() {
        $this->coordenadorDAL = new CoordenadorDAL();
    }
    
    /**
     * Obtém os dados do coordenador pelo ID do usuário
     * 
     * @param int $idUtilizador ID do usuário
     * @return array|false Retorna os dados do coordenador ou false se não encontrado
     */
    public function obterDadosCoordenador($idUtilizador) {
        try {
            return $this->coordenadorDAL->obterPorIdUtilizador($idUtilizador);
        } catch (Exception $e) {
            error_log("Erro ao obter dados do coordenador: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtém as equipes gerenciadas pelo coordenador
     * 
     * @param int $idCoordenador ID do coordenador
     * @return array Lista de equipes gerenciadas
     */
    public function obterEquipesGerenciadas($idCoordenador) {
        try {
            return $this->coordenadorDAL->obterEquipesGerenciadas($idCoordenador);
        } catch (Exception $e) {
            error_log("Erro ao obter equipes gerenciadas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os membros de uma equipe gerenciada pelo coordenador
     * 
     * @param int $idEquipa ID da equipe
     * @param int $idCoordenador ID do coordenador (para verificação de permissão)
     * @return array Lista de membros da equipe
     */
    public function obterMembrosEquipe($idEquipa, $idCoordenador) {
        try {
            return $this->coordenadorDAL->obterMembrosEquipe($idEquipa, $idCoordenador);
        } catch (Exception $e) {
            error_log("Erro ao obter membros da equipe: " . $e->getMessage());
            return [
                'erro' => true,
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtém estatísticas da equipe para o dashboard do coordenador
     * 
     * @param int $idCoordenador ID do coordenador
     * @return array Estatísticas da equipe
     */
    public function obterEstatisticasEquipe($idCoordenador) {
        try {
            return $this->coordenadorDAL->obterEstatisticasEquipe($idCoordenador);
        } catch (Exception $e) {
            error_log("Erro ao obter estatísticas da equipe: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Remove um membro de uma equipe gerenciada pelo coordenador
     * 
     * @param int $idColaborador ID do colaborador a ser removido
     * @param int $idEquipa ID da equipe
     * @return array Retorna um array com o resultado da operação
     */
    public function removerMembroEquipe($idColaborador, $idEquipa) {
        try {
            // Validação básica dos parâmetros
            if (empty($idColaborador) || !is_numeric($idColaborador) || $idColaborador <= 0) {
                return ['success' => false, 'message' => 'ID do colaborador inválido.'];
            }
            
            if (empty($idEquipa) || !is_numeric($idEquipa) || $idEquipa <= 0) {
                return ['success' => false, 'message' => 'ID da equipe inválido.'];
            }
            
            // Verifica se o usuário tem permissão para gerenciar esta equipe
            $equipes = $this->coordenadorDAL->obterEquipesGerenciadas($_SESSION['utilizador_id']);
            $temPermissao = false;
            
            foreach ($equipes as $equipa) {
                if ($equipa['id_equipa'] == $idEquipa) {
                    $temPermissao = true;
                    break;
                }
            }
            
            if (!$temPermissao) {
                return ['success' => false, 'message' => 'Você não tem permissão para gerenciar esta equipe.'];
            }
            
            // Tenta remover o membro da equipe
            $resultado = $this->coordenadorDAL->removerMembroEquipe($idColaborador, $idEquipa);
            
            if ($resultado) {
                return [
                    'success' => true, 
                    'message' => 'Membro removido da equipe com sucesso.'
                ];
            } else {
                return [
                    'success' => false, 
                    'message' => 'Não foi possível remover o membro da equipe. Tente novamente mais tarde.'
                ];
            }
            
        } catch (Exception $e) {
            error_log('Erro na camada BLL ao remover membro da equipe: ' . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.'
            ];
        }
    }
}
