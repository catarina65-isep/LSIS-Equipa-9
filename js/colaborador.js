// Função para atualizar foto de perfil
async function handlePhotoUpload(input) {
    const photoInput = input;
    const photoContainer = document.querySelector('.profile-photo-container');
    const photoImg = photoContainer.querySelector('img');
    const updateButton = document.getElementById('updatePhoto');
    const photoLabel = document.getElementById('profilePhotoLabel');

    const file = photoInput.files[0];
    if (file) {
        // Mostrar preview da foto
        const reader = new FileReader();
        reader.onload = function(e) {
            photoImg.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Atualizar label
        photoLabel.textContent = file.name;
        photoLabel.style.display = 'block';

        // Enviar foto para o servidor
        const formData = new FormData();
        formData.append('profilePhoto', file);

        try {
            updateButton.disabled = true;
            updateButton.innerHTML = `<i class='bx bx-loader bx-spin'></i> Salvando...`;

            const response = await fetch('BLL/colaborador.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Atualizar URL da foto no banco de dados
                photoImg.src = `../uploads/profile_photos/${result.photo_url}`;
                alert('Foto atualizada com sucesso!');
                photoLabel.style.display = 'none';
            } else {
                throw new Error(result.error || 'Erro ao atualizar foto');
            }
        } catch (error) {
            alert('Erro ao atualizar foto: ' + error.message);
        } finally {
            updateButton.disabled = false;
            updateButton.innerHTML = `<i class='bx bx-image'></i> Atualizar Foto`;
        }
    }
}

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
