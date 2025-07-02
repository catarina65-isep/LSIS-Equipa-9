<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../Model/Alerta.php';

class AlertaDAL {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function listarTodos($filtros = []) {
        $sql = "SELECT * FROM alerta WHERE 1=1";
        $params = [];

        // Aplicar filtros
        if (!empty($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }
        
        if (!empty($filtros['prioridade'])) {
            $sql .= " AND prioridade = :prioridade";
            $params[':prioridade'] = $filtros['prioridade'];
        }

        if (!empty($filtros['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        $sql .= " ORDER BY data_criacao DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao listar alertas: ' . $e->getMessage());
            return [];
        }
    }

    public function obterPorId($id) {
        $sql = "SELECT * FROM alerta WHERE id_alerta = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erro ao obter alerta: ' . $e->getMessage());
            return null;
        }
    }

    public function criar(Alerta $alerta) {
        $sql = "INSERT INTO alerta (
            titulo, descricao, tipo, categoria, id_colaborador, 
            id_equipa, id_departamento, data_expiracao, prioridade, 
            status, id_responsavel, id_utilizador_criacao
        ) VALUES (
            :titulo, :descricao, :tipo, :categoria, :id_colaborador, 
            :id_equipa, :id_departamento, :data_expiracao, :prioridade, 
            :status, :id_responsavel, :id_utilizador_criacao
        )";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titulo' => $alerta->getTitulo(),
                ':descricao' => $alerta->getDescricao(),
                ':tipo' => $alerta->getTipo(),
                ':categoria' => $alerta->getCategoria(),
                ':id_colaborador' => $alerta->getIdColaborador(),
                ':id_equipa' => $alerta->getIdEquipa(),
                ':id_departamento' => $alerta->getIdDepartamento(),
                ':data_expiracao' => $alerta->getDataExpiracao(),
                ':prioridade' => $alerta->getPrioridade(),
                ':status' => $alerta->getStatus(),
                ':id_responsavel' => $alerta->getIdResponsavel(),
                ':id_utilizador_criacao' => $alerta->getIdUtilizadorCriacao()
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Erro ao criar alerta: ' . $e->getMessage());
            return false;
        }
    }

    public function atualizar(Alerta $alerta) {
        $sql = "UPDATE alerta SET
            titulo = :titulo,
            descricao = :descricao,
            tipo = :tipo,
            categoria = :categoria,
            id_colaborador = :id_colaborador,
            id_equipa = :id_equipa,
            id_departamento = :id_departamento,
            data_expiracao = :data_expiracao,
            prioridade = :prioridade,
            status = :status,
            id_responsavel = :id_responsavel,
            data_resolucao = :data_resolucao,
            solucao = :solucao,
            id_utilizador_atualizacao = :id_utilizador_atualizacao
        WHERE id_alerta = :id_alerta";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':titulo' => $alerta->getTitulo(),
                ':descricao' => $alerta->getDescricao(),
                ':tipo' => $alerta->getTipo(),
                ':categoria' => $alerta->getCategoria(),
                ':id_colaborador' => $alerta->getIdColaborador(),
                ':id_equipa' => $alerta->getIdEquipa(),
                ':id_departamento' => $alerta->getIdDepartamento(),
                ':data_expiracao' => $alerta->getDataExpiracao(),
                ':prioridade' => $alerta->getPrioridade(),
                ':status' => $alerta->getStatus(),
                ':id_responsavel' => $alerta->getIdResponsavel(),
                ':data_resolucao' => $alerta->getDataResolucao(),
                ':solucao' => $alerta->getSolucao(),
                ':id_utilizador_atualizacao' => $alerta->getIdUtilizadorAtualizacao(),
                ':id_alerta' => $alerta->getIdAlerta()
            ]);
        } catch (PDOException $e) {
            error_log('Erro ao atualizar alerta: ' . $e->getMessage());
            return false;
        }
    }

    public function excluir($id) {
        $sql = "DELETE FROM alerta WHERE id_alerta = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Erro ao excluir alerta: ' . $e->getMessage());
            return false;
        }
    }

    public function atualizarStatus($id, $status, $id_utilizador, $solucao = null) {
        $sql = "UPDATE alerta SET 
            status = :status,
            data_resolucao = :data_resolucao,
            solucao = :solucao,
            id_utilizador_atualizacao = :id_utilizador,
            data_atualizacao = NOW()
        WHERE id_alerta = :id";

        try {
            $dataResolucao = ($status === 'Resolvido') ? date('Y-m-d H:i:s') : null;
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':data_resolucao' => $dataResolucao,
                ':solucao' => $solucao,
                ':id_utilizador' => $id_utilizador,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log('Erro ao atualizar status do alerta: ' . $e->getMessage());
            return false;
        }
    }
}
