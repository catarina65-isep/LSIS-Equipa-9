// Submeter o formulário
async function submitProfileForm() {
    const form = document.getElementById('profileForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const formFields = form.querySelectorAll('input, textarea');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            submitButton.disabled = true;
            submitButton.innerHTML = `<i class='bx bx-loader bx-spin'></i> Salvando...`;

            // Coletar dados do formulário
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Enviar dados para o servidor
            const response = await fetch('BLL/colaborador.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Perfil atualizado com sucesso!');
                // Limpar campos após salvar
                formFields.forEach(field => {
                    if (field.type !== 'date') {
                        field.value = '';
                    } else {
                        field.value = '';
                    }
                });
            } else {
                throw new Error(result.error || 'Erro ao atualizar perfil');
            }
        } catch (error) {
            alert('Erro ao atualizar perfil: ' + error.message);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Guardar Alterações';
        }
    });
}

// Inicializar as funções quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    submitProfileForm();
});
