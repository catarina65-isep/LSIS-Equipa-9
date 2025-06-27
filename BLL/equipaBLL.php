<?php
require_once __DIR__ . '/../DAL/equipaDAL.php';

class EquipaBLL {
    private $equipaDAL;

    public function __construct() {
        $this->equipaDAL = new EquipaDAL();
    }

    public function criarEquipa($dados) {
        // Validação dos dados
        if (empty($dados['nome']) || empty($dados['coordenador_id'])) {
            throw new Exception("Nome da equipe e coordenador são obrigatórios.");
        }

        return $this->equipaDAL->criarEquipa(
            $dados['nome'],
            $dados['descricao'] ?? '',
            $dados['coordenador_id']
        );
    }

    public function adicionarMembro($equipaId, $utilizadorId) {
        return $this->equipaDAL->adicionarMembroEquipa($equipaId, $utilizadorId);
    }

    public function removerMembro($equipaId, $utilizadorId) {
        return $this->equipaDAL->removerMembroEquipa($equipaId, $utilizadorId);
    }

    public function obterEquipa($id) {
        return $this->equipaDAL->obterEquipaPorId($id);
    }

    public function listarEquipas() {
        return $this->equipaDAL->obterTodasEquipas();
    }

    public function atualizarEquipa($id, $dados) {
        if (empty($dados['nome']) || empty($dados['coordenador_id'])) {
            throw new Exception("Nome da equipe e coordenador são obrigatórios.");
        }

        return $this->equipaDAL->atualizarEquipa(
            $id,
            $dados['nome'],
            $dados['descricao'] ?? '',
            $dados['coordenador_id']
        );
    }

    public function excluirEquipa($id) {
        return $this->equipaDAL->excluirEquipa($id);
    }

    public function obterEquipasPorMembro($utilizadorId) {
        return $this->equipaDAL->obterEquipasPorMembro($utilizadorId);
    }
}
?>
