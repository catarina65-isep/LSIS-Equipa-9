<?php
require_once __DIR__ . '/../DAL/equipaDAL.php';

class EquipaBLL {
    private $equipaDAL;

    public function __construct() {
        $this->equipaDAL = new EquipaDAL();
    }
    
    /**
     * Obtém a instância PDO
     * 
     * @return \PDO
     */
    public function getPDO() {
        return $this->equipaDAL->getPDO();
    }

    /**
     * Cria uma nova equipa
     * 
     * @param array $dados Dados da equipa (nome, descricao, coordenador_id)
     * @return int ID da equipa criada
     * @throws Exception Se houver erro na validação dos dados
     */
    public function criarEquipa($dados) {
        // Validação dos dados
        if (empty($dados['nome'])) {
            throw new Exception("O nome da equipa é obrigatório.");
        }
        
        if (empty($dados['coordenador_id'])) {
            throw new Exception("O coordenador da equipa é obrigatório.");
        }
        
        // Verifica se o coordenador existe
        $utilizadorBLL = new UtilizadorBLL();
        $coordenador = $utilizadorBLL->obterPorId($dados['coordenador_id']);
        if (!$coordenador) {
            throw new Exception("O coordenador selecionado não existe.");
        }

        return $this->equipaDAL->criarEquipa(
            $dados['nome'],
            $dados['descricao'] ?? '',
            $dados['coordenador_id']
        );
    }

    /**
     * Adiciona um membro a uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do utilizador
     * @param bool $coordenador Define se o membro é coordenador
     * @return bool Sucesso da operação
     * @throws Exception Se houver erro na validação
     */
    public function adicionarMembro($equipaId, $utilizadorId, $coordenador = false) {
        // Verifica se a equipa existe
        $equipa = $this->obterEquipa($equipaId);
        if (!$equipa) {
            throw new Exception("Equipa não encontrada.");
        }
        
        // Verifica se o utilizador existe
        $utilizadorBLL = new UtilizadorBLL();
        $utilizador = $utilizadorBLL->obterPorId($utilizadorId);
        if (!$utilizador) {
            throw new Exception("Utilizador não encontrado.");
        }
        
        // Se for para adicionar como coordenador, verifica se já existe um
        if ($coordenador) {
            $this->definirCoordenador($equipaId, $utilizadorId);
        }
        
        return $this->equipaDAL->adicionarMembroEquipa($equipaId, $utilizadorId, $coordenador);
    }
    
    /**
     * Define um membro como coordenador da equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do novo coordenador
     * @return bool Sucesso da operação
     * @throws Exception Se houver erro na validação
     */
    public function definirCoordenador($equipaId, $utilizadorId) {
        // Verifica se o membro pertence à equipa
        if (!$this->verificarMembroEquipa($equipaId, $utilizadorId)) {
            throw new Exception("O membro não pertence a esta equipa.");
        }
        
        return $this->equipaDAL->definirCoordenador($equipaId, $utilizadorId);
    }

    /**
     * Remove um membro de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do utilizador
     * @return bool Sucesso da operação
     * @throws Exception Se o membro for o único coordenador da equipa
     */
    public function removerMembro($equipaId, $utilizadorId) {
        // Verifica se o membro é o único coordenador
        $equipa = $this->obterEquipa($equipaId);
        if ($equipa['coordenador_id'] == $utilizadorId) {
            // Conta quantos coordenadores a equipa tem
            $numCoordenadores = $this->equipaDAL->contarCoordenadores($equipaId);
            if ($numCoordenadores <= 1) {
                throw new Exception("Não é possível remover o único coordenador da equipa.");
            }
        }
        
        return $this->equipaDAL->removerMembroEquipa($equipaId, $utilizadorId);
    }

    /**
     * Obtém os dados de uma equipa
     * 
     * @param int $id ID da equipa
     * @return array|false Dados da equipa ou false se não encontrada
     */
    public function obterEquipa($id) {
        $equipa = $this->equipaDAL->obterEquipaPorId($id);
        
        if ($equipa) {
            // Adiciona informações adicionais
            $equipa['subequipas'] = $this->obterSubequipas($id);
            $equipa['membros'] = $this->obterMembrosEquipa($id);
            $equipa['coordenador'] = $this->obterCoordenador($id);
        }
        
        return $equipa;
    }
    
    /**
     * Obtém o coordenador de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @return array|false Dados do coordenador ou false se não encontrado
     */
    public function obterCoordenador($equipaId) {
        return $this->equipaDAL->obterCoordenador($equipaId);
    }
    
    /**
     * Obtém as subequipas de uma equipa
     * 
     * @param int $equipaPaiId ID da equipa pai
     * @return array Lista de subequipas
     */
    public function obterSubequipas($equipaPaiId) {
        return $this->equipaDAL->obterSubequipas($equipaPaiId);
    }
    
    /**
     * Obtém a hierarquia completa de equipas
     * 
     * @param int|null $equipaPaiId ID da equipa pai (null para equipas de nível superior)
     * @return array Hierarquia de equipas
     */
    public function obterHierarquiaEquipas($equipaPaiId = null) {
        $equipas = $this->equipaDAL->obterEquipasPorPai($equipaPaiId);
        
        foreach ($equipas as &$equipa) {
            $equipa['subequipas'] = $this->obterHierarquiaEquipas($equipa['id']);
        }
        
        return $equipas;
    }

    /**
     * Lista todas as equipas
     * 
     * @param bool $incluirInativas Incluir equipas inativas
     * @return array Lista de equipas
     */
    public function listarEquipas($incluirInativas = false) {
        return $this->equipaDAL->obterTodasEquipas($incluirInativas);
    }

    /**
     * Atualiza os dados de uma equipa
     * 
     * @param int $id ID da equipa
     * @param array $dados Novos dados da equipa
     * @return bool Sucesso da operação
     * @throws Exception Se houver erro na validação
     */
    public function atualizarEquipa($id, $dados) {
        error_log('Dados recebidos em atualizarEquipa: ' . print_r($dados, true));
        
        if (empty($dados['nome'])) {
            throw new Exception("O nome da equipa é obrigatório.");
        }
        
        // Verifica se a equipa existe
        $equipaAtual = $this->obterEquipa($id);
        if (!$equipaAtual) {
            throw new Exception("Equipa não encontrada.");
        }
        
        // Se estiver alterando o coordenador, verifica se o novo coordenador existe
        if (!empty($dados['coordenador_id']) && $dados['coordenador_id'] != $equipaAtual['coordenador_id']) {
            $utilizadorBLL = new UtilizadorBLL();
            $novoCoordenador = $utilizadorBLL->obterUtilizadorPorId($dados['coordenador_id']);
            if (!$novoCoordenador) {
                throw new Exception("O coordenador selecionado não existe.");
            }
            
            // Garante que o novo coordenador seja membro da equipa
            if (!$this->verificarMembroEquipa($id, $dados['coordenador_id'])) {
                $this->adicionarMembro($id, $dados['coordenador_id'], true);
            } else {
                $this->definirCoordenador($id, $dados['coordenador_id']);
            }
        } else {
            // Se não estiver alterando o coordenador, usa o coordenador atual
            $dados['coordenador_id'] = $equipaAtual['coordenador_id'];
        }
        
        // Garante que a descrição está definida
        if (!isset($dados['descricao'])) {
            $dados['descricao'] = $equipaAtual['descricao'] ?? '';
        }
        
        error_log('Dados que serão enviados para o DAL: ' . print_r([
            'id' => $id,
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'coordenador_id' => $dados['coordenador_id']
        ], true));
        
        // Chama o método do DAL com os parâmetros corretos
        return $this->equipaDAL->atualizarEquipa(
            $id,
            $dados['nome'],
            $dados['descricao'],
            $dados['coordenador_id']
        );
    }

    /**
     * Exclui uma equipa
     * 
     * @param int $id ID da equipa
     * @return bool Sucesso da operação
     * @throws Exception Se a equipa não puder ser excluída
     */
    public function excluirEquipa($id) {
        // Verifica se a equipa existe
        $equipa = $this->obterEquipa($id);
        if (!$equipa) {
            throw new Exception("Equipa não encontrada.");
        }
        
        // Verifica se a equipa tem subequipas
        $subequipas = $this->obterSubequipas($id);
        if (!empty($subequipas)) {
            throw new Exception("Não é possível excluir uma equipa que possui subequipas.");
        }
        
        // Remove todos os membros da equipa
        $membros = $this->obterMembrosEquipa($id);
        foreach ($membros as $membro) {
            $this->removerMembro($id, $membro['id']);
        }
        
        return $this->equipaDAL->excluirEquipa($id);
    }

    /**
     * Obtém as equipas de um membro
     * 
     * @param int $utilizadorId ID do utilizador
     * @param bool $apenasCoordenadas Apenas equipas onde o utilizador é coordenador
     * @return array Lista de equipas do membro
     */
    public function obterEquipasPorMembro($utilizadorId, $apenasCoordenadas = false) {
        return $this->equipaDAL->obterEquipasPorMembro($utilizadorId, $apenasCoordenadas);
    }
    
    /**
     * Obtém os membros de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param bool $apenasAtivos Apenas membros ativos
     * @return array Lista de membros
     */
    public function obterMembrosEquipa($equipaId, $apenasAtivos = true) {
        return $this->equipaDAL->obterMembrosEquipa($equipaId, $apenasAtivos);
    }
    
    /**
     * Verifica se um utilizador é membro de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do utilizador
     * @return bool True se for membro, false caso contrário
     */
    public function verificarMembroEquipa($equipaId, $utilizadorId) {
        return $this->equipaDAL->verificarMembroEquipa($equipaId, $utilizadorId);
    }
    
    /**
     * Conta o número de membros de uma equipa
     * 
     * @param int $equipaId ID da equipa
     * @return int Número de membros
     * @throws Exception Se a equipa não for encontrada
     */
    public function contarMembrosEquipa($equipaId) {
        // Verifica se a equipa existe
        $equipa = $this->obterEquipa($equipaId);
        if (!$equipa) {
            throw new Exception("Equipa não encontrada.");
        }
        
        return $this->equipaDAL->contarMembrosEquipa($equipaId);
    }
    
    /**
     * Verifica se um utilizador é coordenador de alguma equipa
     * 
     * @param int $utilizadorId ID do utilizador
     * @return bool True se for coordenador de alguma equipa, false caso contrário
     */
    public function isCoordenador($utilizadorId) {
        return $this->equipaDAL->isCoordenador($utilizadorId);
    }
    

    
    /**
     * Obtém uma equipa com os seus membros
     * 
     * @param int $equipaId ID da equipa
     * @return array|false Dados da equipa com membros ou false se não encontrada
     */
    public function obterEquipaComMembros($equipaId) {
        $equipa = $this->obterEquipa($equipaId);
        
        if ($equipa) {
            // Garante que os membros estão carregados
            if (!isset($equipa['membros'])) {
                $equipa['membros'] = $this->obterMembrosEquipa($equipaId);
            }
            
            // Garante que o coordenador está na lista de membros
            if (!empty($equipa['coordenador_id'])) {
                $coordenadorEncontrado = false;
                foreach ($equipa['membros'] as $membro) {
                    if ($membro['id'] == $equipa['coordenador_id']) {
                        $coordenadorEncontrado = true;
                        break;
                    }
                }
                
                // Se o coordenador não estiver na lista de membros, adiciona
                if (!$coordenadorEncontrado) {
                    $coordenador = $this->obterCoordenador($equipaId);
                    if ($coordenador) {
                        $equipa['membros'][] = $coordenador;
                    }
                }
            }
        }
        
        return $equipa;
    }
}
?>
