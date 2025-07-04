<?php
require_once __DIR__ . '/../DAL/UtilizadorDAL.php';

class UtilizadorBLL {
    private $utilizadorDAL;

    public function __construct() {
        $this->utilizadorDAL = new UtilizadorDAL();
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

    public function obterPorId($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID de usuário inválido');
            }
            
            return $this->utilizadorDAL->obterPorId($id);
        } catch (Exception $e) {
            error_log('Erro ao obter usuário: ' . $e->getMessage());
            return null;
        }
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
     * Obtém a lista de coordenadores (usuários com perfil de coordenador)
     * @return array Lista de coordenadores
     */
    public function obterCoordenadores() {
        try {
            // Assumindo que o perfil de coordenador tem ID 2
            // Ajuste o ID conforme sua estrutura de perfis
            return $this->utilizadorDAL->listarPorPerfil(2);
        } catch (Exception $e) {
            error_log('Erro ao obter coordenadores: ' . $e->getMessage());
            return [];
        }
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
     * Lista todos os usuários ativos
     * @return array Lista de usuários ativos
     */
    public function listarUtilizadoresAtivos() {
        try {
            // A DAL já filtra por usuários ativos
            return $this->utilizadorDAL->listarTodos();
        } catch (Exception $e) {
            error_log('Erro ao listar usuários ativos: ' . $e->getMessage());
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
            $camposObrigatorios = ['id_utilizador', 'nome', 'email', 'username', 'id_perfilacesso'];
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
