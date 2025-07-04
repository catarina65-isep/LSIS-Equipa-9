    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- JavaScript do Colaborador -->
    <script src="js/colaborador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/locale/pt.js"></script>
    
    <!-- Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    
    <!-- Input Mask -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script src="/LSIS-Equipa-9/UI/assets/js/script.js"></script>
    
    <script>
        // Configuração global do AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Ocorreu um erro ao processar sua requisição.';
                
                if (xhr.status === 422) {
                    // Erros de validação
                    const errors = xhr.responseJSON.errors;
                    errorMessage = '';
                    for (const field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                } else if (xhr.status === 403) {
                    errorMessage = 'Você não tem permissão para executar esta ação.';
                } else if (xhr.status === 404) {
                    errorMessage = 'O recurso solicitado não foi encontrado.';
                } else if (xhr.status >= 500) {
                    errorMessage = 'Ocorreu um erro no servidor. Por favor, tente novamente mais tarde.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: errorMessage,
                    confirmButtonColor: 'var(--primary-color)'
                });
            }
        });
        
        // Inicialização dos componentes
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Inicializa popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Inicializa Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção',
                allowClear: true,
                dropdownParent: $('.modal') // Para funcionar dentro de modais
            });
            
            // Máscaras de entrada
            $('.cpf').mask('000.000.000-00');
            $('.telefone').mask('(00) 00000-0000');
            $('.cep').mask('00000-000');
            $('.data').mask('00/00/0000');
            
            // Inicializa Date Range Picker
            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    firstDay: 1
                }
            });
            
            $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            });
            
            // Configuração global do DataTables
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                      "<'row'<'col-sm-12'tr>>" +
                      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bx bx-copy me-1"></i> Copiar',
                        className: 'btn btn-sm btn-outline-secondary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bx bx-file me-1"></i> Excel',
                        className: 'btn btn-sm btn-outline-success',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bx bxs-file-pdf me-1"></i> PDF',
                        className: 'btn btn-sm btn-outline-danger',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bx bx-printer me-1"></i> Imprimir',
                        className: 'btn btn-sm btn-outline-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="bx bx-columns me-1"></i> Colunas',
                        className: 'btn btn-sm btn-outline-info'
                    }
                ]
            });
            
            // Inicializa DataTables nas tabelas que não foram inicializadas
            $('table.datatable').not('.no-datatable').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        responsive: true,
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                              "<'row'<'col-sm-12'tr>>" +
                              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                    });
                }
            });
            
            // Confirmação para ações de exclusão
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const title = $(this).data('title') || 'Tem certeza?';
                const text = $(this).data('text') || 'Esta ação não pode ser desfeita!';
                
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--danger-color)',
                    cancelButtonColor: 'var(--secondary)',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
            
            // Validação de formulários
            $('form.needs-validation').on('submit', function(e) {
                const form = this;
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(form).addClass('was-validated');
                    
                    // Rola até o primeiro campo inválido
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        $('html, body').animate({
                            scrollTop: $(firstInvalid).offset().top - 100
                        }, 500);
                        firstInvalid.focus();
                    }
                }
            });
            
            // Limpa validação ao fechar o modal
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form').removeClass('was-validated')[0].reset();
                $(this).find('.invalid-feedback').remove();
                $(this).find('.is-invalid').removeClass('is-invalid');
            });
            
            // Mostra/esconde senha
            $('.toggle-password').on('click', function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-show').addClass('bx-hide');
                }
            });
        });
        
        // Função para exibir mensagens de sucesso/erro
        function showAlert(type, message, title = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            
            Toast.fire({
                icon: type,
                title: title,
                text: message
            });
        }
        
        // Exibe mensagens flash do PHP
        <?php if (isset($_SESSION['flash_message'])): ?>
            showAlert('<?= $_SESSION['flash_message']['type'] ?>', '<?= addslashes($_SESSION['flash_message']['message']) ?>', '<?= addslashes($_SESSION['flash_message']['title'] ?? '') ?>');
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        
        // Função para carregar conteúdo via AJAX
        function loadContent(url, target, callback) {
            $(target).html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
            
            $.get(url, function(response) {
                $(target).html(response);
                if (typeof callback === 'function') {
                    callback();
                }
            }).fail(function() {
                $(target).html('<div class="alert alert-danger">Erro ao carregar o conteúdo. Por favor, tente novamente.</div>');
            });
        }
        
        // Função para enviar formulário via AJAX
        function submitForm(form, successCallback) {
            const $form = $(form);
            const $submitBtn = $form.find('button[type="submit"]');
            const submitBtnText = $submitBtn.html();
            
            // Desabilita o botão de submit
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processando...');
            
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method') || 'POST',
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.message) {
                        showAlert('success', response.message, response.title || 'Sucesso');
                        if (typeof successCallback === 'function') {
                            successCallback(response);
                        }
                        // Recarrega a página após 1.5 segundos se não houver redirecionamento
                        if (!response.preventReload) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    }
                },
                complete: function() {
                    // Reabilita o botão de submit
                    $submitBtn.prop('disabled', false).html(submitBtnText);
                }
            });
            
            return false;
        }
    </script>