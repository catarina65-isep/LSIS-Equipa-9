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
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #fff;
            background-color: #1a237e; /* Azul escuro */
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: transparent;
            min-height: 100vh;
            color: #fff;
        }
        
        .guest-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .guest-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .guest-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .guest-header p {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }
        
        .form-section {
            background: var(--light-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .form-section h3 i {
            font-size: 1.2em;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            flex: 1;
            min-width: 250px;
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        .form-select {
            width: 100%;
            padding: 0.6rem 2.25rem 0.6rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }
        
        .required-field::after {
            content: ' *';
            color: var(--danger-color);
        }
        
        .form-note {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
            font-style: italic;
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

            <form id="guestForm" enctype="multipart/form-data">
                <!-- Identificação -->
                <div class="form-section">
                    <h3><i class="bi bi-person-badge-fill"></i> Identificação</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_completo" class="form-label required-field">Nome Completo</label>
                            <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
                        </div>
                        <div class="form-group">
                            <label for="data_nascimento" class="form-label required-field">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nif" class="form-label required-field">Número de Identificação Fiscal (NIF)</label>
                            <input type="text" class="form-control" id="nif" name="nif" maxlength="9" pattern="\d{9}" required>
                            <div class="form-note">Apenas números (9 dígitos)</div>
                        </div>
                        <div class="form-group">
                            <label for="sexo" class="form-label required-field">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Selecione...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="situacao_irs" class="form-label">Situação IRS</label>
                            <select class="form-select" id="situacao_irs" name="situacao_irs">
                                <option value="">Selecione...</option>
                                <option value="Solteiro">Solteiro(a)</option>
                                <option value="Casado">Casado(a)</option>
                                <option value="Uniao_Facto">União de Facto</option>
                                <option value="Divorciado">Divorciado(a)</option>
                                <option value="Viuvo">Viúvo(a)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="irs_jovem" class="form-label">IRS Jovem</label>
                            <select class="form-select" id="irs_jovem" name="irs_jovem">
                                <option value="Nao">Não</option>
                                <option value="Sim">Sim</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="niss" class="form-label">Número de Segurança Social (NISS)</label>
                            <input type="text" class="form-control" id="niss" name="niss" maxlength="11">
                        </div>
                        <div class="form-group">
                            <label for="cc" class="form-label">Número do Cartão de Cidadão</label>
                            <input type="text" class="form-control" id="cc" name="cc">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nacionalidade" class="form-label">Nacionalidade</label>
                            <input type="text" class="form-control" id="nacionalidade" name="nacionalidade" value="Portuguesa">
                        </div>
                        <div class="form-group">
                            <label for="dependentes" class="form-label">Número de Dependentes</label>
                            <input type="number" class="form-control" id="dependentes" name="dependentes" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cartaocidadao" class="form-label required-field">Cartão de Cidadão</label>
                            <input type="file" class="form-control" id="cartaocidadao" name="cartaocidadao" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-note">Fotografia ou digitalização do Cartão de Cidadão (frente e verso)</div>
                        </div>
                    </div>
                </div>

                <!-- Morada de Residência -->
                <div class="form-section">
                    <h3><i class="bi bi-house-door-fill"></i> Morada de Residência</h3>
                    <div class="form-group">
                        <label for="morada" class="form-label required-field">Morada Completa</label>
                        <input type="text" class="form-control" id="morada" name="morada" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="localidade" class="form-label required-field">Localidade</label>
                            <input type="text" class="form-control" id="localidade" name="localidade" required>
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal" class="form-label required-field">Código Postal</label>
                            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="comprovativo_morada" class="form-label required-field">Comprovativo de Morada</label>
                            <input type="file" class="form-control" id="comprovativo_morada" name="comprovativo_morada" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-note">Formatos aceites: PDF, JPG, PNG (tamanho máximo: 5MB)</div>
                        </div>
                    </div>
                </div>

                <!-- Contactos -->
                <div class="form-section">
                    <h3><i class="bi bi-telephone-fill"></i> Contactos</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telemovel" class="form-label required-field">Telemóvel</label>
                            <input type="tel" class="form-control" id="telemovel" name="telemovel" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label required-field">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="iban" class="form-label">IBAN</label>
                            <input type="text" class="form-control" id="iban" name="iban">
                        </div>
                    </div>
                </div>

                <!-- Contacto de Emergência -->
                <div class="form-section">
                    <h3><i class="bi bi-person-heart"></i> Contacto de Emergência</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_emergencia" class="form-label">Nome do Contacto de Emergência</label>
                            <input type="text" class="form-control" id="nome_emergencia" name="nome_emergencia">
                        </div>
                        <div class="form-group">
                            <label for="telefone_emergencia" class="form-label">Contacto de Emergência</label>
                            <input type="tel" class="form-control" id="telefone_emergencia" name="telefone_emergencia">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="parentesco_emergencia" class="form-label">Grau de Parentesco</label>
                            <input type="text" class="form-control" id="parentesco_emergencia" name="parentesco_emergencia" placeholder="Ex: Mãe, Pai, Cônjuge, etc.">
                        </div>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="form-section">
                    <h3><i class="bi bi-car-front-fill"></i> Veículo</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="matricula" class="form-label">Matrícula do Carro</label>
                            <input type="text" class="form-control" id="matricula" name="matricula">
                        </div>
                    </div>
                </div>

                <!-- Termos e Condições -->
                <div class="form-section">
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termos" name="termos" required>
                            <label class="form-check-label" for="termos">
                                Declaro que as informações fornecidas são verdadeiras e aceito os 
                                <a href="#" data-bs-toggle="modal" data-bs-target="#termosModal">termos e condições</a>.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i> Limpar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Submeter Pedido
                    </button>
                </div>
            </form>

            <!-- Modal Termos e Condições -->
            <div class="modal fade" id="termosModal" tabindex="-1" aria-labelledby="termosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="termosModalLabel">Termos e Condições</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <h6>1. Tratamento de Dados Pessoais</h6>
                            <p>Os dados pessoais recolhidos serão tratados de acordo com o Regulamento Geral de Proteção de Dados (RGPD) e demais legislação aplicável, sendo utilizados exclusivamente para os fins de gestão de visitas e segurança das instalações.</p>
                            
                            <h6>2. Acesso às Instalações</h6>
                            <p>O acesso às instalações está condicionado à prévia autorização e ao cumprimento das normas de segurança em vigor. É obrigatória a apresentação de documento de identificação válido no momento da visita.</p>
                            
                            <h6>3. Horário de Visitas</h6>
                            <p>As visitas deverão ocorrer dentro do horário comercial, salvo autorização prévia. A duração máxima por visita é de 8 horas.</p>
                            
                            <h6>4. Segurança</h6>
                            <p>É estritamente proibido o acesso a áreas restritas não autorizadas. O visitante deve seguir as instruções de segurança fornecidas pelo responsável.</p>
                            
                            <h6>5. Cancelamento</h6>
                            <p>Pedidos de cancelamento devem ser efetuados com pelo menos 24 horas de antecedência.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicialização do datepicker e timepicker
        document.addEventListener('DOMContentLoaded', function() {
            // Definir data mínima como hoje
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById('data_visita').min = hoje;
            
            // Carregar responsáveis (exemplo)
            const responsaveis = [
                { id: 1, nome: 'João Silva' },
                { id: 2, nome: 'Maria Santos' },
                { id: 3, nome: 'Carlos Oliveira' }
            ];
            
            const selectResponsavel = document.getElementById('responsavel');
            responsaveis.forEach(responsavel => {
                const option = document.createElement('option');
                option.value = responsavel.id;
                option.textContent = responsavel.nome;
                selectResponsavel.appendChild(option);
            });
            
            // Máscara para NIF
            const nifInput = document.getElementById('nif');
            if (nifInput) {
                nifInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    e.target.value = value;
                });
            }
            
            // Máscara para telefone
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 9) value = value.substring(0, 9);
                    if (value.length > 0) {
                        value = value.replace(/^(\d{2,3})(\d{3})(\d{3})$/, '$1 $2 $3');
                    }
                    e.target.value = value;
                });
            });
            
            // Máscara para código postal
            const codigoPostalInput = document.getElementById('codigo_postal');
            if (codigoPostalInput) {
                codigoPostalInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 7) value = value.substring(0, 7);
                    if (value.length > 4) {
                        value = value.replace(/^(\d{4})(\d{3})$/, '$1-$2');
                    }
                    e.target.value = value;
                });
            }
            
            // Validação do formulário
            const form = document.getElementById('guestForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!form.checkValidity()) {
                        e.stopPropagation();
                        form.classList.add('was-validated');
                        return;
                    }
                    
                    submitGuestForm();
                });
            }
        });
        
        function submitGuestForm() {
            const form = document.getElementById('guestForm');
            const formData = new FormData(form);
            
            // Adicionar campos adicionais se necessário
            formData.append('data_submissao', new Date().toISOString());
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
