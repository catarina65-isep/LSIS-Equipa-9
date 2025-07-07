            </div> <!-- Fechamento do container-fluid py-4 -->
        </div> <!-- Fechamento do main-content -->
    </div> <!-- Fechamento do wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Box Icons -->
    <script src='https://unpkg.com/boxicons@2.1.4/dist/boxicons.js'></script>
    
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script src="/LSIS-Equipa-9/UI/assets/js/main.js"></script>
    
    <!-- Inicialização de plugins -->
    <script>
        $(document).ready(function() {
            // Inicializa o DataTable
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().destroy();
            }
            
            $('.datatable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json"
                },
                "responsive": true,
                "pageLength": 10,
                "order": [[0, 'desc']],
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "drawCallback": function() {
                    // Reativa os tooltips após o redesenho da tabela
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });
            
            // Ativa os tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Ativa os popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Fecha o menu mobile ao clicar em um item
            $('.nav-link').on('click', function() {
                const sidebar = document.querySelector('.sidebar');
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });
        
        // Função para confirmar exclusão
        function confirmarExclusao(event, message = 'Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.') {
            if (!confirm(message)) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        
        // Função para mostrar loading em botões
        function showButtonLoading(button, text = 'Processando...') {
            const $button = $(button);
            $button.attr('data-original-text', $button.html());
            $button.prop('disabled', true);
            $button.html(`
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                ${text}
            `);
        }
        
        // Função para esconder loading em botões
        function hideButtonLoading(button) {
            const $button = $(button);
            $button.html($button.attr('data-original-text'));
            $button.prop('disabled', false);
        }
    </script>
    
    <!-- Scripts específicos da página -->
    <?php if (function_exists('page_scripts')) { page_scripts(); } ?>
    
    </body>
</html>
