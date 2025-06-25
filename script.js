document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const loginForm = document.getElementById('loginForm');
    const guestLink = document.getElementById('guestLink');
    const showRegisterLink = document.getElementById('showRegister');
    const forgotPasswordLink = document.getElementById('forgotPassword');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const closeModal = document.getElementById('closeModal');
    const closeSuccess = document.getElementById('closeSuccess');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const successMessage = document.getElementById('successMessage');
    const recoveryEmail = document.getElementById('recoveryEmail');

    // Abrir modal de recuperação de senha
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            forgotPasswordModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    }

    // Fechar modal
    function closeForgotPasswordModal() {
        forgotPasswordModal.classList.remove('show');
        document.body.style.overflow = 'auto';
        if (forgotPasswordForm && successMessage) {
            forgotPasswordForm.reset();
            successMessage.style.display = 'none';
            forgotPasswordForm.style.display = 'block';
        }
    }

    if (closeModal) closeModal.addEventListener('click', closeForgotPasswordModal);
    if (closeSuccess) closeSuccess.addEventListener('click', closeForgotPasswordModal);

    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === forgotPasswordModal) {
            closeForgotPasswordModal();
        }
    });

    // Submeter formulário de recuperação de senha
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = recoveryEmail ? recoveryEmail.value.trim() : '';
            
            if (!email) {
                alert('Por favor, insira o seu email.');
                return;
            }
            
            // Enviar formulário via AJAX
            const formData = new FormData(this);
            
            fetch('recuperar_senha.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    if (forgotPasswordForm && successMessage) {
                        forgotPasswordForm.style.display = 'none';
                        successMessage.style.display = 'block';
                    }
                } else {
                    alert(data.message || 'Ocorreu um erro ao processar sua solicitação.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao processar sua solicitação.');
            });
        });
    }

    // Evento para o link de convidado
    if (guestLink) {
        guestLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'acesso_convidado.php';
        });
    }

    // Evento para o link de registro
    if (showRegisterLink) {
        showRegisterLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'registro.php';
        });
    }
});