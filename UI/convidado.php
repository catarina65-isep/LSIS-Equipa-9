<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo à Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style-rh.css">
    <link rel="stylesheet" href="style-guest.css">
    <style>
        .guest-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .guest-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .guest-header h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .guest-header p {
            color: #6c757d;
        }
        
        .guest-card {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .guest-card h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        
        .guest-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .guest-form .form-group {
            flex: 1;
            min-width: 250px;
        }
        
        .guest-submit {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .guest-button {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .guest-button-primary {
            background-color: #2c3e50;
            color: white;
        }
        
        .guest-button-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="guest-container">
            <div class="guest-header">
                <h1>Bem-vindo à Tlantic</h1>
                <p>Área de acesso para convidados</p>
            </div>

            <div class="guest-card">
                <h3>Agendar Visita</h3>
                <form id="guestForm" class="guest-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control" id="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="cv" class="form-label">Curriculum Vitae</label>
                        <input type="file" class="form-control" id="cv" accept=".pdf" required>
                        <div class="form-text">
                            Selecione um arquivo PDF do seu CV (máximo 5MB)
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="visitDate" class="form-label">Data da Visita</label>
                        <input type="date" class="form-control" id="visitDate" required>
                    </div>
                    <div class="form-group">
                        <label for="visitTime" class="form-label">Hora da Visita</label>
                        <input type="time" class="form-control" id="visitTime" required>
                    </div>
                    <div class="form-group">
                        <label for="purpose" class="form-label">Propósito da Visita</label>
                        <textarea class="form-control" id="purpose" rows="3" required></textarea>
                    </div>
                </form>
                <div class="guest-submit">
                    <button type="button" class="guest-button guest-button-secondary" onclick="window.location.href='index.php'">
                        Cancelar
                    </button>
                    <button type="button" class="guest-button guest-button-primary" onclick="submitGuestForm()">
                        Agendar Visita
                    </button>
                </div>
            </div>

            <div class="guest-card">
                <h3>Informações Úteis</h3>
                <div class="info-section">
                    <div class="info-item">
                        <i class="bi bi-clock"></i>
                        <div class="info-content">
                            <h4>Horário de Funcionamento</h4>
                            <p>Segunda a Sexta: 09:00 - 18:00</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div class="info-content">
                            <h4>Localização</h4>
                            <p>R. Manuel Pinto de Azevedo 626, 1º Piso<br>4100-320 Porto</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <div class="info-content">
                            <h4>Contato</h4>
                            <p>+351 22 016 0060<br>info@tlantic.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function submitGuestForm() {
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('cv', document.getElementById('cv').files[0]);
            formData.append('visitDate', document.getElementById('visitDate').value);
            formData.append('visitTime', document.getElementById('visitTime').value);
            formData.append('purpose', document.getElementById('purpose').value);

            fetch('api/guest.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Visita agendada com sucesso!');
                    window.location.href = 'index.php';
                } else {
                    alert('Erro ao agendar visita: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro ao processar a solicitação: ' + error.message);
            });
        }

        // Adicionar validação de tipo de arquivo
        document.getElementById('cv').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    alert('Por favor, selecione um arquivo PDF.');
                    e.target.value = '';
                } else if (file.size > 5 * 1024 * 1024) { // 5MB
                    alert('O arquivo é muito grande. O máximo permitido é 5MB.');
                    e.target.value = '';
                }
            }
        });
    </script>
</body>
</html>
