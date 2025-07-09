            </div><!-- Fecha container-fluid -->
        </div><!-- Fecha main-content -->
    </div><!-- Fecha wrapper -->

    <!-- Scripts JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Inicialização de tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Toggle da barra lateral em telas pequenas
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.body.classList.toggle('sidebar-toggled');
                    sidebar.classList.toggle('toggled');
                    
                    if (window.innerWidth <= 992) {
                        if (sidebar.classList.contains('toggled')) {
                            const collapseElementList = [].slice.call(document.querySelectorAll('.sidebar .collapse'));
                            collapseElementList.map(function (collapseEl) {
                                return new bootstrap.Collapse(collapseEl, { toggle: false }).hide();
                            });
                        }
                    }
                });
            }

            // Fechar o menu quando clicar fora
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 992) {
                    const isClickInside = sidebar.contains(event.target) || 
                                        (sidebarToggle && sidebarToggle.contains(event.target));
                    
                    if (!isClickInside && sidebar.classList.contains('toggled')) {
                        document.body.classList.remove('sidebar-toggled');
                        sidebar.classList.remove('toggled');
                    }
                }
            });

            // Atualizar a data e hora
            function atualizarDataHora() {
                const agora = new Date();
                const opcoes = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: 'Europe/Lisbon'
                };
                
                const elementosDataHora = document.querySelectorAll('[data-atualizar-datahora]');
                if (elementosDataHora.length > 0) {
                    elementosDataHora.forEach(function(elemento) {
                        elemento.textContent = agora.toLocaleDateString('pt-PT', opcoes);
                    });
                }
            }

            // Atualizar a cada minuto
            setInterval(atualizarDataHora, 60000);
            atualizarDataHora(); // Chamada inicial

            // Adiciona classe para animação de carregamento
            document.body.classList.add('loaded');
        });

        // Função para exibir mensagens de notificação
        function mostrarNotificacao(mensagem, tipo = 'info') {
            const tipos = {
                'success': 'Sucesso!',
                'error': 'Erro!',
                'warning': 'Aviso!',
                'info': 'Informação'
            };

            const toastContainer = document.createElement('div');
            toastContainer.className = `toast align-items-center text-white bg-${tipo} border-0`;
            toastContainer.setAttribute('role', 'alert');
            toastContainer.setAttribute('aria-live', 'assertive');
            toastContainer.setAttribute('aria-atomic', 'true');
            
            toastContainer.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${tipos[tipo] || ''}</strong> ${mensagem}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            `;

            const toast = new bootstrap.Toast(toastContainer, {
                autohide: true,
                delay: 5000
            });

            document.body.appendChild(toastContainer);
            toast.show();

            // Remove o toast do DOM após ser escondido
            toastContainer.addEventListener('hidden.bs.toast', function () {
                document.body.removeChild(toastContainer);
            });
        }

        // Exemplo de como usar a função de notificação:
        // mostrarNotificacao('Operação realizada com sucesso!', 'success');
        // mostrarNotificacao('Ocorreu um erro ao processar sua solicitação.', 'error');
    </script>
</body>
</html>
