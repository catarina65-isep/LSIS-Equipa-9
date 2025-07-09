<?php
require_once __DIR__ . '/../DAL/UtilizadorDAL.php';

class UtilizadorBLL {
    private $utilizadorDAL;

    public function __construct() {
        $this->utilizadorDAL = new UtilizadorDAL();
    }
    
    /**
     * Obtém a instância PDO
     * 
     * @return \PDO
     */
    public function getPDO() {
        return $this->utilizadorDAL->getPDO();
    }
    
    /**
     * Conta quantos usuários estão associados a um perfil específico
     * @param int $idPerfil ID do perfil de acesso
     * @return int Número de usuários associados ao perfil
     */
    public function contarPorPerfil($idPerfil) {
        try {
            if (!is_numeric($idPerfil) || $idPerfil <= 0) {
                throw new Exception('ID de perfil inválido');
            }
            
            return $this->utilizadorDAL->contarPorPerfil($idPerfil);
            
        } catch (Exception $e) {
            error_log('Erro ao contar usuários por perfil: ' . $e->getMessage());
            return 0;
        }
    }

    public function listarTodos() {
        try {
            return $this->utilizadorDAL->listarTodos();
        } catch (Exception $e) {
            error_log('Erro ao listar usuários: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém a lista de coordenadores ativos diretamente da tabela coordenador
     * 
     * @return array Lista de coordenadores com id_utilizador, nome, email e cargo
     */
    public function obterCoordenadores() {
        try {
            error_log("Iniciando consulta de coordenadores ativos...");
            
            // Verificar se a conexão PDO está disponível
            $pdo = $this->getPDO();
            if (!$pdo) {
                error_log("Erro: Não foi possível obter a conexão PDO");
                return [];
            }
            
            $sql = "SELECT 
                        c.id_coordenador,
                        u.id_utilizador,
                        COALESCE(CONCAT(
                            COALESCE(col.nome, u.username), 
                            CASE WHEN col.apelido IS NOT NULL THEN CONCAT(' ', col.apelido) ELSE '' END
                        ), u.username) as nome,
                        COALESCE(u.email, col.email) as email,
                        c.cargo,
                        c.tipo_coordenacao,
                        c.ativo as coordenador_ativo,
                        u.ativo as usuario_ativo
                    FROM coordenador c
                    INNER JOIN utilizador u ON c.id_utilizador = u.id_utilizador
                    LEFT JOIN colaborador col ON u.id_utilizador = col.id_utilizador
                    WHERE c.ativo = 1 
                    AND u.ativo = 1
                    ORDER BY 
                        CASE WHEN col.nome IS NOT NULL THEN col.nome ELSE u.username END,
                        CASE WHEN col.apelido IS NOT NULL THEN col.apelido ELSE '' END";
            
            error_log("SQL: $sql");
            
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Erro ao executar a consulta: " . print_r($errorInfo, true));
                return [];
            }
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Total de coordenadores encontrados: " . count($resultados));
            
            // Log detalhado de cada coordenador encontrado
            foreach ($resultados as $i => $coord) {
                error_log(sprintf(
                    "Coordenador #%d: ID=%d, Nome=%s, Email=%s, Cargo=%s, Tipo=%s, Coordenador Ativo=%d, Usuário Ativo=%d",
                    $i + 1,
                    $coord['id_utilizador'],
                    $coord['nome'],
                    $coord['email'],
                    $coord['cargo'],
                    $coord['tipo_coordenacao'],
                    $coord['coordenador_ativo'],
                    $coord['usuario_ativo']
                ));
            }
            
            return $resultados;
            
        } catch (Exception $e) {
            $erro = 'Erro ao listar coordenadores: ' . $e->getMessage();
            error_log($erro);
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    /**
     * Lista todos os utilizadores ativos com informações de colaborador
     * 
     * @return array Lista de utilizadores ativos com informações adicionais
     */
    public function listarUtilizadoresAtivos() {
        try {
            error_log("Iniciando consulta de utilizadores ativos...");
            
            $sql = "SELECT 
                        u.id_utilizador,
                        u.username,
                        u.email,
                        u.ativo,
                        u.id_colaborador,
                        COALESCE(CONCAT(c.nome, ' ', c.apelido), u.username) as nome_completo,
                        c.cargo,
                        c.departamento,
                        c.data_entrada,
                        c.estado
                    FROM utilizador u
                    LEFT JOIN colaborador c ON u.id_colaborador = c.id_colaborador
                    WHERE u.ativo = 1
                    ORDER BY c.nome, c.apelido, u.username";
            
            error_log("SQL para listar utilizadores ativos: $sql");
            
            $stmt = $this->getPDO()->prepare($sql);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Total de utilizadores ativos encontrados: " . count($resultados));
            
            // Log detalhado dos primeiros 5 usuários para depuração
            $totalLog = min(5, count($resultados));
            for ($i = 0; $i < $totalLog; $i++) {
                $user = $resultados[$i];
                error_log(sprintf(
                    "Utilizador #%d: ID=%d, Nome=%s, Email=%s, Cargo=%s",
                    $i + 1,
                    $user['id_utilizador'],
                    $user['nome_completo'],
                    $user['email'],
                    $user['cargo'] ?? 'Não definido'
                ));
            }
            
            if (count($resultados) > 5) {
                error_log("... e mais " . (count($resultados) - 5) . " utilizadores");
            }
            
            return $resultados;
            
        } catch (Exception $e) {
            $erro = 'Erro ao listar utilizadores ativos: ' . $e->getMessage();
            error_log($erro);
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }
    


    /**
     * Obtém um usuário pelo ID, verificando se ele é um coordenador ativo
     * 
     * @param int $id ID do usuário
     * @return array|false Dados do usuário ou false se não encontrado/inativo
     */
    public function obterPorId($id, $verificarCoordenador = false) {
        try {
            error_log("Iniciando obterPorId - ID: $id, Verificar Coordenador: " . ($verificarCoordenador ? 'Sim' : 'Não'));
            
            if (!is_numeric($id) || $id <= 0) {
                error_log("ID inválido: $id");
                return false;
            }
            
            // Busca o usuário no banco de dados usando o método da DAL
            $usuario = $this->utilizadorDAL->obterPorId($id);
            
            if (!$usuario) {
                error_log("Usuário com ID $id não encontrado na tabela utilizador");
                return false;
            }
            
            error_log("Dados do usuário encontrado: " . print_r($usuario, true));
            
            // Verifica se o usuário está ativo
            if (isset($usuario['ativo']) && $usuario['ativo'] != 1) {
                error_log("Usuário com ID $id está inativo");
                return false;
            }
            
            // Se precisar verificar se é coordenador
            if ($verificarCoordenador) {
                // Verifica se o usuário tem o perfil de coordenador (id_perfil_acesso = 3)
                $idPerfilAcesso = $usuario['id_perfil_acesso'] ?? $usuario['id_perfilacesso'] ?? null;
                
                error_log("Verificando perfil de coordenador - ID Perfil: " . ($idPerfilAcesso ?? 'nulo'));
                
                if ($idPerfilAcesso != 3) { // 3 é o ID do perfil de coordenador
                    error_log("Usuário com ID $id não tem perfil de coordenador (perfil: $idPerfilAcesso)");
                    return false; // Não é um coordenador
                }
                
                error_log("Usuário com ID $id é um coordenador válido");
            }
            
            // Formata os dados para manter compatibilidade
            $dadosUsuario = [
                'id_utilizador' => $usuario['id_utilizador'],
                'username' => $usuario['username'] ?? '',
                'email' => $usuario['email'] ?? '',
                // Garante compatibilidade com ambos os nomes de campo
                'id_perfil_acesso' => $usuario['id_perfil_acesso'] ?? $usuario['id_perfilacesso'] ?? null,
                'id_perfilacesso' => $usuario['id_perfilacesso'] ?? $usuario['id_perfil_acesso'] ?? null,
                'perfil' => $usuario['perfil'] ?? null,
                'id_colaborador' => $usuario['id_colaborador'] ?? null,
                'nome' => ($usuario['nome'] ?? $usuario['username']) . (isset($usuario['apelido']) ? ' ' . $usuario['apelido'] : ''),
                'ativo' => $usuario['ativo'] ?? 0
            ];
            
            error_log("Dados do usuário retornados: " . print_r($dadosUsuario, true));
            
            return $dadosUsuario;
            
        } catch (Exception $e) {
            error_log('Erro ao obter usuário: ' . $e->getMessage());
            return null;
        }
    }
    

    
    /**
     * Obtém um usuário pelo ID (alias para obterPorId para compatibilidade)
     * 
     * @param int $id ID do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function obterUtilizadorPorId($id) {
        return $this->obterPorId($id);
    }
    
    /**
     * Conta o número total de usuários ativos
     * 
     * @return int Número total de usuários ativos
     */
    public function contarTotal() {
        try {
            return $this->utilizadorDAL->contarTotal();
        } catch (Exception $e) {
            error_log('Erro ao contar usuários: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtém estatísticas de usuários
     * 
     * @return array Estatísticas de usuários
     */
    public function obterEstatisticas() {
        try {
            $totalUsuarios = $this->contarTotal();
            $usuariosUltimoMes = $this->contarUsuariosUltimoMes();
            $usuariosMesAnterior = $this->contarUsuariosPorPeriodo(
                date('Y-m-01', strtotime('first day of last month')),
                date('Y-m-t', strtotime('last day of last month'))
            );
            
            $variacao = $usuariosMesAnterior > 0 
                ? round((($usuariosUltimoMes - $usuariosMesAnterior) / $usuariosMesAnterior) * 100) 
                : ($usuariosUltimoMes > 0 ? 100 : 0);
            
            return [
                'total_usuarios' => $totalUsuarios,
                'novos_ultimo_mes' => $usuariosUltimoMes,
                'variacao_usuarios' => $variacao,
                'distribuicao_perfil' => $this->obterDistribuicaoPorPerfil(),
                'usuarios_por_equipa' => $this->contarUsuariosPorEquipa()
            ];
        } catch (Exception $e) {
            error_log('Erro ao obter estatísticas de usuários: ' . $e->getMessage());
            return [
                'total_usuarios' => 0,
                'novos_ultimo_mes' => 0,
                'variacao_usuarios' => 0,
                'distribuicao_perfil' => [],
                'usuarios_por_equipa' => []
            ];
        }
    }
    
    /**
     * Conta usuários criados no último mês
     * 
     * @return int Número de usuários criados no último mês
     */
    public function contarUsuariosUltimoMes() {
        try {
            $primeiroDiaMesAtual = date('Y-m-01');
            $ultimoDiaMesAtual = date('Y-m-t');
            
            return $this->contarUsuariosPorPeriodo($primeiroDiaMesAtual, $ultimoDiaMesAtual);
        } catch (Exception $e) {
            error_log('Erro ao contar usuários do último mês: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Conta usuários criados em um período específico
     * 
     * @param string $dataInicio Data de início (YYYY-MM-DD)
     * @param string $dataFim Data de fim (YYYY-MM-DD)
     * @return int Número de usuários criados no período
     */
    public function contarUsuariosPorPeriodo($dataInicio, $dataFim) {
        try {
            return $this->utilizadorDAL->contarPorPeriodo($dataInicio, $dataFim);
        } catch (Exception $e) {
            error_log('Erro ao contar usuários por período: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtém a distribuição de usuários por perfil
     * 
     * @return array Distribuição de usuários por perfil
     */
    public function obterDistribuicaoPorPerfil() {
        try {
            return $this->utilizadorDAL->obterDistribuicaoPorPerfil();
        } catch (Exception $e) {
            error_log('Erro ao obter distribuição por perfil: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Conta usuários por equipe
     * 
     * @return array Contagem de usuários por equipe
     */
    public function contarUsuariosPorEquipa() {
        try {
            return $this->utilizadorDAL->contarUsuariosPorEquipa();
        } catch (Exception $e) {
            error_log('Erro ao contar usuários por equipe: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém dados para o dashboard
     * 
     * @return array Dados para o dashboard
     */
    public function obterDadosParaDashboard() {
        return [
            'total_usuarios' => $this->contarTotal(),
            'novos_usuarios_ultimo_mes' => $this->contarUsuariosUltimoMes(),
            'distribuicao_perfil' => $this->obterDistribuicaoPorPerfil(),
            'usuarios_por_equipa' => $this->contarUsuariosPorEquipa()
        ];
    }


    
    /**
     * Obtém a lista de funcionários (usuários ativos que podem ser membros de equipe)
     * @return array Lista de funcionários
     */
    public function obterFuncionarios() {
        try {
            // Ajuste os critérios conforme necessário
            return $this->utilizadorDAL->listarAtivos();
        } catch (Exception $e) {
            error_log('Erro ao obter funcionários: ' . $e->getMessage());
            return [];
        }
    }
    
    
    /**
     * Atualiza os dados de um usuário
     * @param array $dados Dados do usuário a serem atualizados
     * @return bool True em caso de sucesso, False caso contrário
     */
    public function atualizar($dados) {
        try {
            // Validar dados obrigatórios
            $camposObrigatorios = ['id_utilizador', 'nome', 'email', 'username', 'id_perfil_acesso']; // Corrigido o nome do campo
            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo]) || $dados[$campo] === '') {
                    throw new Exception("O campo {$campo} é obrigatório.");
                }
            }
            
            // Validar e-mail
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('O endereço de e-mail não é válido.');
            }
            
            // Verificar se o usuário existe
            $usuarioExistente = $this->obterPorId($dados['id_utilizador']);
            if (!$usuarioExistente) {
                throw new Exception('Usuário não encontrado.');
            }
            
            // Obter o usuário existente para pegar o id_colaborador se não estiver nos dados
            $usuarioExistente = $this->obterPorId($dados['id_utilizador']);
            error_log('Dados do usuário existente: ' . print_r($usuarioExistente, true));
            
            if ($usuarioExistente && !empty($usuarioExistente['id_colaborador'])) {
                $dados['id_colaborador'] = $usuarioExistente['id_colaborador'];
                error_log('ID do colaborador encontrado: ' . $dados['id_colaborador']);
            } else {
                error_log('Nenhum ID de colaborador encontrado para o usuário: ' . $dados['id_utilizador']);
            }
            
            error_log('Dados a serem enviados para a DAL: ' . print_r($dados, true));
            
            // Chamar a DAL para atualizar
            $resultado = $this->utilizadorDAL->atualizar($dados);
            error_log('Resultado da atualização: ' . ($resultado ? 'Sucesso' : 'Falha'));
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log('Erro ao atualizar usuário: ' . $e->getMessage());
            throw $e;
        }
    }
}
