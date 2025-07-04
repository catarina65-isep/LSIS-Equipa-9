// Verifica se o DataTables já foi carregado
if (typeof jQuery.fn.DataTable === 'function') {
    // Verifica se a tabela já foi inicializada
    if (!$.fn.DataTable.isDataTable('#tabelaCampos')) {
        // Inicialização do DataTable
        const tabela = $('#tabelaCampos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                search: "Pesquisar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "Nenhum registro encontrado",
                info: "Mostrando _PAGE_ de _PAGES_",
                infoEmpty: "Nenhum registro disponível",
                infoFiltered: "(filtrado de _MAX_ registros totais)",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Próxima",
                    previous: "Anterior"
                }
            },
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [5] } // Desativa ordenação na coluna de ações
            ]
        });
        
        console.log('DataTable inicializado com sucesso!');
    } else {
        console.log('DataTable já foi inicializado anteriormente.');
    }
    
    // Inicializar Select2 se ainda não foi inicializado
    if ($.fn.select2 && !$('.select2-container').length) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione as opções',
            allowClear: true
        });
    }
    
    // Resto do código JavaScript...
    // [Seu código existente continua aqui]
    
} else {
    console.error('DataTables não foi carregado corretamente!');
}
