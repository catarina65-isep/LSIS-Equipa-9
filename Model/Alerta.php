<?php
class Alerta {
    private $id_alerta;
    private $titulo;
    private $descricao;
    private $tipo;
    private $categoria;
    private $id_colaborador;
    private $id_equipa;
    private $id_departamento;
    private $data_criacao;
    private $data_expiracao;
    private $prioridade;
    private $status;
    private $id_responsavel;
    private $data_resolucao;
    private $solucao;
    private $id_utilizador_criacao;
    private $id_utilizador_atualizacao;
    private $data_atualizacao;

    // Getters e Setters
    public function getIdAlerta() { return $this->id_alerta; }
    public function getTitulo() { return $this->titulo; }
    public function getDescricao() { return $this->descricao; }
    public function getTipo() { return $this->tipo; }
    public function getCategoria() { return $this->categoria; }
    public function getIdColaborador() { return $this->id_colaborador; }
    public function getIdEquipa() { return $this->id_equipa; }
    public function getIdDepartamento() { return $this->id_departamento; }
    public function getDataCriacao() { return $this->data_criacao; }
    public function getDataExpiracao() { return $this->data_expiracao; }
    public function getPrioridade() { return $this->prioridade; }
    public function getStatus() { return $this->status; }
    public function getIdResponsavel() { return $this->id_responsavel; }
    public function getDataResolucao() { return $this->data_resolucao; }
    public function getSolucao() { return $this->solucao; }
    public function getIdUtilizadorCriacao() { return $this->id_utilizador_criacao; }
    public function getIdUtilizadorAtualizacao() { return $this->id_utilizador_atualizacao; }
    public function getDataAtualizacao() { return $this->data_atualizacao; }

    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }
    public function setIdColaborador($id_colaborador) { $this->id_colaborador = $id_colaborador; }
    public function setIdEquipa($id_equipa) { $this->id_equipa = $id_equipa; }
    public function setIdDepartamento($id_departamento) { $this->id_departamento = $id_departamento; }
    public function setDataExpiracao($data_expiracao) { $this->data_expiracao = $data_expiracao; }
    public function setPrioridade($prioridade) { $this->prioridade = $prioridade; }
    public function setStatus($status) { $this->status = $status; }
    public function setIdResponsavel($id_responsavel) { $this->id_responsavel = $id_responsavel; }
    public function setDataResolucao($data_resolucao) { $this->data_resolucao = $data_resolucao; }
    public function setSolucao($solucao) { $this->solucao = $solucao; }
    public function setIdUtilizadorCriacao($id_utilizador_criacao) { $this->id_utilizador_criacao = $id_utilizador_criacao; }
    public function setIdUtilizadorAtualizacao($id_utilizador_atualizacao) { $this->id_utilizador_atualizacao = $id_utilizador_atualizacao; }
}
