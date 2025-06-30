<?php
// Configurações de alertas automáticos
return [
    // Alertas de aniversário
    'aniversario' => [
        'periodicidade' => 365, // dias
        'antes' => 15, // dias antes do aniversário
        'tipo' => 'aniversario',
        'assunto' => 'Aniversário do Colaborador'
    ],

    // Alertas de contrato
    'contrato' => [
        'periodicidade' => 365, // dias
        'antes' => 30, // dias antes do vencimento
        'tipo' => 'contrato',
        'assunto' => 'Atualização Contratual'
    ],

    // Alertas de voucher
    'voucher' => [
        'periodicidade' => 23, // meses
        'antes' => 1, // mês antes do vencimento
        'tipo' => 'voucher',
        'assunto' => 'Voucher de Telemóvel'
    ],

    // Alertas de documento
    'documento' => [
        'periodicidade' => 365, // dias
        'antes' => 30, // dias antes do vencimento
        'tipo' => 'documento',
        'assunto' => 'Atualização de Documento'
    ]
];
?>
