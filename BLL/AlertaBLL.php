<?php
require_once __DIR__ . '/../DAL/AlertaDAL.php';
require_once __DIR__ . '/../Model/Alerta.php';

class AlertaBLL {
    private $alertaDAL;

    public function __construct() {
        $this->alertaDAL = new AlertaDAL();
    }

    public function listarAlertas($filtros = []) {
        return $this->alertaDAL->listarTodos($filtros);
    }

    public function obterAlerta($id) {
        return $this->alertaDAL->obterPorId($id);
    }

    public function criarAlerta($dados) {
        $alerta = new Alerta();
        $alerta->setTitulo($dados['titulo']);
        $alerta->setDescricao($dados['descricao']);
        $alerta->setTipo($dados['tipo']);
        $alerta->setCategoria($dados['categoria'] ?? null);
        $alerta->setIdColaborador($dados['id_colaborador'] ?? null);
        $alerta->setIdEquipa($dados['id_equipa'] ?? null);
        $alerta->setIdDepartamento($dados['id_departamento'] ?? null);
        $alerta->setDataExpiracao($dados['data_expiracao'] ?? null);
        $alerta->setPrioridade($dados['prioridade'] ?? 'Média');
        $alerta->setStatus('Pendente');
        $alerta->setIdResponsavel($dados['id_responsavel'] ?? null);
        $alerta->setIdUtilizadorCriacao($_SESSION['user_id']);

        return $this->alertaDAL->criar($alerta);
    }

    public function atualizarAlerta($dados) {
        $alerta = new Alerta();
        $alerta->setIdAlerta($dados['id_alerta']);
        $alerta->setTitulo($dados['titulo']);
        $alerta->setDescricao($dados['descricao']);
        $alerta->setTipo($dados['tipo']);
        $alerta->setCategoria($dados['categoria'] ?? null);
        $alerta->setIdColaborador($dados['id_colaborador'] ?? null);
        $alerta->setIdEquipa($dados['id_equipa'] ?? null);
        $alerta->setIdDepartamento($dados['id_departamento'] ?? null);
        $alerta->setDataExpiracao($dados['data_expiracao'] ?? null);
        $alerta->setPrioridade($dados['prioridade']);
        $alerta->setStatus($dados['status']);
        $alerta->setIdResponsavel($dados['id_responsavel'] ?? null);
        $alerta->setDataResolucao($dados['data_resolucao'] ?? null);
        $alerta->setSolucao($dados['solucao'] ?? null);
        $alerta->setIdUtilizadorAtualizacao($_SESSION['user_id']);

        return $this->alertaDAL->atualizar($alerta);
    }

    public function excluirAlerta($id) {
        return $this->alertaDAL->excluir($id);
    }

    public function atualizarStatusAlerta($id, $status, $solucao = null) {
        return $this->alertaDAL->atualizarStatus(
            $id, 
            $status, 
            $_SESSION['user_id'], 
            $solucao
        );
    }

    public function obterTiposAlerta() {
        return [
            'Aviso' => 'Aviso',
            'Alerta' => 'Alerta',
            'Informação' => 'Informação',
            'Urgente' => 'Urgente'
        ];
    }

    public function obterPrioridades() {
        return [
            'Baixa' => 'Baixa',
            'Média' => 'Média',
            'Alta' => 'Alta',
            'Crítica' => 'Crítica'
        ];
    }

    public function obterStatus() {
        return [
            'Pendente' => 'Pendente',
            'Em Andamento' => 'Em Andamento',
            'Resolvido' => 'Resolvido',
            'Cancelado' => 'Cancelado'
        ];
    }
}
