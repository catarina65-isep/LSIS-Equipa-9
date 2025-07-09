<?php
require_once __DIR__ . '/../DAL/equipaDAL.php';
require_once __DIR__ . '/../DAL/ColaboradorDAL.php';

class EquipaBLL {
    private $equipaDAL;
    private $colaboradorDAL;

    public function __construct() {
        $this->equipaDAL = new EquipaDAL();
        $this->colaboradorDAL = new ColaboradorDAL();
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
        
        // Obter o ID do colaborador diretamente da tabela utilizador
        $utilizadorId = (int)$dados['coordenador_id'];
        
        if ($utilizadorId <= 0) {
            throw new Exception("ID de coordenador inválido.");
        }
        
        $pdo = $this->getPDO();
       

        // 1. Verifica se o usuário existe e está ativo
        $sql = "SELECT id_utilizador, ativo FROM utilizador WHERE id_utilizador = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $utilizadorId, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            throw new Exception("Usuário não encontrado.");
        }

        // Verifica se o usuário está ativo
        if (!isset($usuario['ativo']) || $usuario['ativo'] != 1) {
            throw new Exception("O usuário selecionado não está ativo.");
        }
        
        // 2. Verifica se o usuário é um coordenador ativo e obtém o id_coordenador
        $sqlCoordenador = "SELECT id_coordenador, ativo, tipo_coordenacao 
                          FROM coordenador 
                          WHERE id_utilizador = :id_utilizador AND ativo = 1";
        $stmtCoordenador = $pdo->prepare($sqlCoordenador);
        $stmtCoordenador->bindParam(':id_utilizador', $utilizadorId, PDO::PARAM_INT);
        $stmtCoordenador->execute();
        $coordenador = $stmtCoordenador->fetch(PDO::FETCH_ASSOC);
        
        if (!$coordenador) {
            throw new Exception("Apenas coordenadores ativos podem ser designados como coordenadores de equipe. Por favor, selecione um usuário que já seja um coordenador ativo no sistema.");
        }
        
        // 3. Obtém o id_coordenador para usar na chave estrangeira
        $idCoordenador = (int)$coordenador['id_coordenador'];
        error_log("ID do coordenador a ser usado: " . $idCoordenador);

        $pdo = $this->getPDO();
        $inTransaction = $pdo->inTransaction();
        
        try {
            if (!$inTransaction) {
                $pdo->beginTransaction();
            }


            $equipaId = $this->equipaDAL->criarEquipa(
                $dados['nome'],
                $dados['descricao'] ?? '',
                $idCoordenador  // Usando o id_coordenador obtido da tabela coordenador
            );
            
            //  $l1 = (!empty($dados['membros'])) ? "true" : "false";
            //  $l2 = (is_array($dados['membros'])) ? "true" : "false";
                      
            // if (true) {
            //         throw new Exception("pois 10 ... " . $l1 ." - " . $l2);
            //     }

            // Primeiro, definimos o coordenador da equipe
            $this->definirCoordenador($equipaId, $dados['coordenador_id']);
            
            // Depois, adicionamos os membros da equipe, se houver
            if (!empty($dados['membros']) && is_array($dados['membros'])) {
                $membros = array_map('intval', $dados['membros']);
                
                // Remove o coordenador da lista de membros, se estiver presente
                $membros = array_diff($membros, [$dados['coordenador_id']]);
                
                // Adiciona os membros da equipe
                foreach ($membros as $membroId) {
                    $this->adicionarMembro($equipaId, $membroId, false);
                }
            }
            
            if (!$inTransaction) {
                $pdo->commit();
            }
            return $equipaId;
            
        } catch (Exception $e) {
            if (!$inTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao criar equipe: ' . $e->getMessage());
            throw new Exception("Erro ao criar a equipe: " . $e->getMessage());
        }
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
        $pdo = $this->getPDO();
        $inTransaction = $pdo->inTransaction();
        

        try {
            if (!$inTransaction) {
                $pdo->beginTransaction();
            }
            

            // Verifica se a equipa existe
            // $equipa = $this->obterEquipa($equipaId);
            // if (!$equipa===false) {
            //     throw new Exception("Equipa não encontrada.");
            // }

            
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

 
            $resultado = $this->equipaDAL->adicionarMembroEquipa($equipaId, $utilizadorId, $coordenador);
            
            if (!$inTransaction) {
                $pdo->commit();
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            if (!$inTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao adicionar membro à equipe: ' . $e->getMessage());
            throw new Exception("Erro ao adicionar membro à equipe: " . $e->getMessage());
        }
    }
    
    /**
     * Define um membro como coordenador da equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do novo coordenador (id_utilizador)
     * @return bool Sucesso da operação
     * @throws Exception Se houver erro na validação
     */
    /**
     * Define um membro como coordenador da equipa
     * 
     * @param int $equipaId ID da equipa
     * @param int $utilizadorId ID do novo coordenador (id_utilizador)
     * @return bool Sucesso da operação
     * @throws Exception Se houver erro na validação
     */
    public function definirCoordenador($equipaId, $utilizadorId) {
        $pdo = $this->getPDO();
        $inTransaction = $pdo->inTransaction();
        
        try {
            if (!$inTransaction) {
                $pdo->beginTransaction();
            }
            
            error_log("Iniciando definição de coordenador. Equipa ID: $equipaId, Utilizador ID: $utilizadorId");
            
            // Verifica se o usuário existe
            $utilizadorBLL = new UtilizadorBLL();
            $utilizador = $utilizadorBLL->obterPorId($utilizadorId);
            
            if (!$utilizador) {
                error_log("ERRO: Utilizador com ID $utilizadorId não encontrado.");
                throw new Exception("Utilizador não encontrado.");
            }
            
            // O coordenador não precisa ser membro da equipe
            // Removida a verificação de membro da equipe
            
            // Busca informações completas do coordenador na tabela coordenador
            $sql = "SELECT 
                        c.id_coordenador, 
                        c.ativo, 
                        c.id_utilizador, 
                        c.tipo_coordenacao,
                        c.id_equipa,
                        u.username, 
                        u.email,
                        col.nome, 
                        col.apelido
                    FROM coordenador c
                    JOIN utilizador u ON c.id_utilizador = u.id_utilizador
                    LEFT JOIN colaborador col ON u.id_utilizador = col.id_utilizador
                    WHERE c.id_utilizador = :id_utilizador 
                    AND c.ativo = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_utilizador' => $utilizadorId]);
            $coordenador = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Dados do coordenador: " . print_r($coordenador, true));
            
            if (!$coordenador) {
                error_log("ERRO: O usuário ID $utilizadorId não está cadastrado como coordenador ativo");
                throw new Exception("O usuário selecionado não está cadastrado como coordenador ativo.");
            }
            
            // Verifica se o coordenador tem permissão para coordenar esta equipe
            // Se for um coordenador de equipe específica e já estiver associado a outra equipe, não permite
            if ($coordenador['tipo_coordenacao'] === 'Equipa' && 
                !empty($coordenador['id_equipa']) && 
                $coordenador['id_equipa'] != $equipaId) {
                error_log("ERRO: Coordenador ID {$coordenador['id_coordenador']} já está associado à equipe ID {$coordenador['id_equipa']}");
                throw new Exception("O coordenador selecionado já está associado a outra equipe.");
            }
            
            // Atualiza a equipe com o id_coordenador correto
            $sqlUpdate = "UPDATE equipa SET id_coordenador = :id_coordenador WHERE id_equipa = :equipa_id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $result = $stmtUpdate->execute([
                ':id_coordenador' => $coordenador['id_coordenador'], // Usando o id_coordenador da tabela coordenador
                ':equipa_id' => $equipaId
            ]);
            
            if (!$result) {
                $error = $stmtUpdate->errorInfo();
                error_log("ERRO ao atualizar coordenador da equipe: " . print_r($error, true));
                throw new Exception("Erro ao atualizar o coordenador da equipe: " . $error[2]);
            }
            
            // Atualiza o registro do coordenador para vincular à equipe
            // Se for um coordenador de equipe específica, atualiza o vínculo
            if ($coordenador['tipo_coordenacao'] === 'Equipa') {
                $sqlUpdateCoordenador = "UPDATE coordenador SET id_equipa = :equipa_id WHERE id_coordenador = :id_coordenador";
                $stmtUpdateCoordenador = $pdo->prepare($sqlUpdateCoordenador);
                $resultCoordenador = $stmtUpdateCoordenador->execute([
                    ':equipa_id' => $equipaId,
                    ':id_coordenador' => $coordenador['id_coordenador']
                ]);
                
                if (!$resultCoordenador) {
                    $error = $stmtUpdateCoordenador->errorInfo();
                    error_log("AVISO: Não foi possível atualizar o vínculo do coordenador com a equipe: " . print_r($error, true));
                    // Não lançamos exceção aqui, pois a operação principal foi bem-sucedida
                }
            }
            
            if (!$inTransaction) {
                $pdo->commit();
            }
            
            return true;
            
        } catch (PDOException $e) {
            if (!$inTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("ERRO no banco de dados ao definir coordenador: " . $e->getMessage());
            throw new Exception("Erro ao processar a definição do coordenador: " . $e->getMessage());
        } catch (Exception $e) {
            if (!$inTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("ERRO ao verificar coordenador: " . $e->getMessage());
            throw new Exception("Erro ao verificar as informações do coordenador: " . $e->getMessage());
        }
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

        if ($equipa===false) {
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
            $novoCoordenador = $utilizadorBLL->obterPorId($dados['coordenador_id']);
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
            /*if (!empty($equipa['coordenador_id'])) {
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
            }*/
        }
        
        return $equipa;
    }

    /**
     * Conta o número total de equipes ativas
     * 
     * @return int Número total de equipes ativas
     */
    public function contarTotal() {
        return $this->equipaDAL->contarTotal();
    }
    
    /**
     * Lista as equipes com estatísticas básicas
     * 
     * @return array Lista de equipes com estatísticas
     */
    public function listarComEstatisticas() {
        $equipas = $this->equipaDAL->listarTodas();
        
        foreach ($equipas as &$equipa) {
            $equipa['total_membros'] = $this->contarMembros($equipa['id_equipa']);
            $equipa['membros_ativos'] = $this->contarMembrosAtivos($equipa['id_equipa']);
            $equipa['membros_afastados'] = $this->contarMembrosAfastados($equipa['id_equipa']);
            $equipa['ultima_atualizacao'] = $this->obterUltimaAtualizacao($equipa['id_equipa']);
        }
        
        return $equipas;
    }
    
    /**
     * Obtém a distribuição de colaboradores por equipe
     * 
     * @return array Distribuição de colaboradores por equipe
     */
    public function obterDistribuicaoPorEquipa() {
        return $this->equipaDAL->obterDistribuicaoPorEquipa();
    }
    
    /**
     * Conta o número de membros de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return int Número de membros
     */
    private function contarMembros($idEquipa) {
        return $this->equipaDAL->contarMembros($idEquipa);
    }
    
    /**
     * Conta o número de membros ativos de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return int Número de membros ativos
     */
    private function contarMembrosAtivos($idEquipa) {
        return $this->equipaDAL->contarMembrosPorStatus($idEquipa, 'Ativo');
    }
    
    /**
     * Conta o número de membros afastados de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return int Número de membros afastados
     */
    private function contarMembrosAfastados($idEquipa) {
        return $this->equipaDAL->contarMembrosPorStatus($idEquipa, 'Afastado');
    }
    
    /**
     * Obtém a data da última atualização de uma equipe
     * 
     * @param int $idEquipa ID da equipe
     * @return string Data da última atualização
     */
    private function obterUltimaAtualizacao($idEquipa) {
        return $this->equipaDAL->obterUltimaAtualizacao($idEquipa);
    }
    
    /**
     * Obtém dados para o dashboard
     * 
     * @return array Dados para o dashboard
     */
    public function obterDadosParaDashboard() {
        return [
            'total_equipas' => $this->contarTotal(),
            'distribuicao_equipas' => $this->obterDistribuicaoPorEquipa(),
            'equipas' => $this->listarComEstatisticas()
        ];
    }
}
