<?php
require_once __DIR__ . '/../DAL/ColaboradorDAL.php';

class ColaboradorBLL {
    private $colaboradorDAL;

    public function __construct() {
        $this->colaboradorDAL = new ColaboradorDAL();
    }

    public function contarTotal() {
        return $this->colaboradorDAL->contarTotal();
    }

    public function obterEstatisticas() {
        $estatisticas = [
            'total' => $this->contarTotal(),
            'mes_atual' => $this->contarAdmissoesMesAtual(),
            'mes_anterior' => $this->contarAdmissoesMesAnterior(),
            'variacao_colaboradores' => $this->calcularVariacaoMensal(),
            'distribuicao_cargo' => $this->obterDistribuicaoPorCargo(),
            'distribuicao_departamento' => $this->obterDistribuicaoPorDepartamento()
        ];

        return $estatisticas;
    }

    public function contarAdmissoesMesAtual() {
        return $this->colaboradorDAL->contarAdmissoesPorPeriodo(
            date('Y-m-01'),
            date('Y-m-t')
        );
    }

    public function contarAdmissoesMesAnterior() {
        $primeiroDiaMesAnterior = date('Y-m-01', strtotime('first day of last month'));
        $ultimoDiaMesAnterior = date('Y-m-t', strtotime('last day of last month'));
        
        return $this->colaboradorDAL->contarAdmissoesPorPeriodo(
            $primeiroDiaMesAnterior,
            $ultimoDiaMesAnterior
        );
    }

    public function calcularVariacaoMensal() {
        $mesAtual = $this->contarAdmissoesMesAtual();
        $mesAnterior = $this->contarAdmissoesMesAnterior();

        if ($mesAnterior == 0) {
            return $mesAtual > 0 ? 100 : 0;
        }

        return round((($mesAtual - $mesAnterior) / $mesAnterior) * 100);
    }

    public function obterAdmissoesUltimos12Meses() {
        $dataFinal = new DateTime();
        $dataInicial = (new DateTime())->modify('-11 months');
        
        $periodo = new DatePeriod(
            new DateTime($dataInicial->format('Y-m-01')),
            new DateInterval('P1M'),
            new DateTime($dataFinal->format('Y-m-t'))
        );

        $admissoesPorMes = [];
        foreach ($periodo as $data) {
            $mesAno = $data->format('Y-m');
            $primeiroDia = $data->format('Y-m-01');
            $ultimoDia = $data->format('Y-m-t');
            
            $admissoesPorMes[$mesAno] = $this->colaboradorDAL->contarAdmissoesPorPeriodo(
                $primeiroDia,
                $ultimoDia
            );
        }

        return $admissoesPorMes;
    }

    public function obterDistribuicaoPorCargo() {
        return $this->colaboradorDAL->obterDistribuicaoPorCargo();
    }

    public function obterDistribuicaoPorDepartamento() {
        return $this->colaboradorDAL->obterDistribuicaoPorDepartamento();
    }
}
