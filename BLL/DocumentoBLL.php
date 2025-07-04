<?php
require_once __DIR__ . '/../DAL/DocumentoDAL.php';

class DocumentoBLL {
    private $documentoDAL;

    public function __construct() {
        $this->documentoDAL = new DocumentoDAL();
    }

    /**
     * Conta o número total de documentos pendentes
     * 
     * @return int Número de documentos pendentes
     */
    public function contarPendentes() {
        return $this->documentoDAL->contarPorStatus('Pendente');
    }

    /**
     * Lista os próximos documentos a vencer
     * 
     * @param int $limite Número máximo de documentos a retornar
     * @return array Lista de documentos
     */
    public function listarProximosVencimentos($limite = 5) {
        return $this->documentoDAL->listarProximosVencimentos($limite);
    }

    /**
     * Obtém estatísticas de documentos
     * 
     * @return array Estatísticas de documentos
     */
    public function obterEstatisticas() {
        return [
            'total' => $this->documentoDAL->contarTotal(),
            'pendentes' => $this->contarPendentes(),
            'aprovados' => $this->documentoDAL->contarPorStatus('Aprovado'),
            'expirados' => $this->documentoDAL->contarExpirados(),
            'proximos_vencimentos' => $this->listarProximosVencimentos(5)
        ];
    }

    /**
     * Obtém dados para o dashboard
     * 
     * @return array Dados para o dashboard
     */
    public function obterDadosParaDashboard() {
        return [
            'documentos_pendentes' => $this->contarPendentes(),
            'documentos_proximos_vencer' => $this->documentoDAL->contarProximosVencer(7), // Próximos 7 dias
            'documentos_expirados' => $this->documentoDAL->contarExpirados(),
            'documentos_por_tipo' => $this->documentoDAL->obterContagemPorTipo()
        ];
    }
}
