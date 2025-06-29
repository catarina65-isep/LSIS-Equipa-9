function handlePhotoUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validar tamanho do arquivo (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'O arquivo deve ter no máximo 5MB');
            return;
        }

        // Validar formato do arquivo
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showAlert('error', 'Formato de arquivo não suportado. Por favor, use JPEG, PNG ou GIF.');
            return;
        }

        // Pré-visualizar a foto
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('foto-preview');
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Esconder todas as seções inicialmente
    document.querySelectorAll('.profile-section').forEach(function(section) {
        section.style.display = 'none';
    });

    // Adicionar evento de click para mostrar/ocultar seções
    document.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('href');
            const section = document.querySelector(sectionId);
            if (section) {
                // Esconder todas as outras seções
                document.querySelectorAll('.profile-section').forEach(function(s) {
                    s.style.display = 'none';
                    s.classList.remove('active');
                });
                
                // Mostrar a seção selecionada
                section.style.display = 'block';
                section.classList.add('active');
                
                // Atualizar classe active no menu
                document.querySelectorAll('.nav-link').forEach(function(navLink) {
                    navLink.classList.remove('active');
                });
                this.classList.add('active');

                // Scroll suave para a seção
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Carregar dados do usuário
    loadUserData();
    // Carrega os dados do usuário
    loadUserData();
    loadDocuments();
    loadBenefits();

    // Inicializa tema
    initializeTheme();

    // Inicializa sidebar
    initializeSidebar();
});

// Função para inicializar o tema
function initializeTheme() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        
        // Atualiza o ícone inicial
        const icon = themeToggle.querySelector('i');
        icon.className = savedTheme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';

        themeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Atualiza o ícone
            const icon = this.querySelector('i');
            icon.className = newTheme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
        });
    }
}

// Função para inicializar a sidebar
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        // Verifica o estado do sidebar
        if (localStorage.getItem('sidebarToggled') === 'true') {
            document.body.classList.add('sidebar-toggled');
        }

        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
            localStorage.setItem('sidebarToggled', document.body.classList.contains('sidebar-toggled'));
        });
    }
}

// Função para criar um documento
function createDocumentItem(document) {
    return `
        <div class="document-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">${document.nome}</h3>
                    <p class="text-muted mb-1">${document.tipo}</p>
                    <span class="status-badge ${document.status === 'aprovado' ? 'status-badge-approved' : 
                        document.status === 'pendente' ? 'status-badge-pending' : 'status-badge-expired'}">
                        ${document.status}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadDocument('${document.id}')">
                        <i class='bx bx-download'></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDocument('${document.id}')">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </div>
            <p class="text-muted small">Data de Validade: ${document.data_validade}</p>
        </div>
    `;
}

// Função para criar um benefício
function createBenefitItem(benefit) {
    return `
        <div class="benefit-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">${benefit.nome}</h3>
                    <p class="text-muted mb-1">${benefit.tipo}</p>
                    <span class="status-badge ${benefit.status === 'ativo' ? 'status-badge-success' : 'status-badge-expired'}">
                        ${benefit.status}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewBenefit('${benefit.id}')">
                        <i class='bx bx-info-circle'></i>
                    </button>
                </div>
            </div>
            <p class="text-muted small">Data de Início: ${benefit.data_inicio}</p>
        </div>
    `;
}

// Função para adicionar documento
const addDocumentBtn = document.getElementById('addDocument');
if (addDocumentBtn) {
    addDocumentBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        modal.show();
    });
}

// Função para fazer upload de documento
const uploadDocumentBtn = document.getElementById('uploadDocument');
if (uploadDocumentBtn) {
    uploadDocumentBtn.addEventListener('click', function() {
        const form = document.getElementById('documentForm');
        const formData = new FormData(form);
        
        // Adicionar ID do usuário
        formData.append('usuario_id', <?php echo $_SESSION['usuario_id']; ?>);
        
        // Validar arquivo
        const fileInput = document.getElementById('documentFile');
        const file = fileInput.files[0];
        
        if (!file) {
            showAlert('error', 'Por favor, selecione um arquivo');
            return;
        }
        
        // Validar tamanho do arquivo (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'O arquivo deve ter no máximo 5MB');
            return;
        }

        // Validar tipo de arquivo
        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                           'image/jpeg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            showAlert('error', 'Formato de arquivo não suportado. Por favor, use PDF, DOC, DOCX, JPG ou PNG.');
            return;
        }

        // Mostrar loading
        uploadDocumentBtn.disabled = true;
        uploadDocumentBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...';

        // Enviar para o servidor
        fetch('../DAL/documentos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('documentModal'));
                modal.hide();
                
                // Limpar formulário
                form.reset();
                
                // Atualizar lista de documentos
                loadDocuments();
                
                showAlert('success', 'Documento adicionado com sucesso!');
            } else {
                showAlert('error', 'Erro ao adicionar documento: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao adicionar documento');
        })
        .finally(() => {
            // Restaurar botão
            uploadDocumentBtn.disabled = false;
            uploadDocumentBtn.innerHTML = 'Upload';
        });
    });
}

// Função para adicionar benefício
const addBenefitBtn = document.getElementById('addBenefit');
if (addBenefitBtn) {
    addBenefitBtn.addEventListener('click', function() {
        window.location.href = 'beneficios.php';
    });
}

// Função para inicializar máscaras
function initializeMasks() {
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,3})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '');
        });
    }

    const nifInput = document.getElementById('nif');
    if (nifInput) {
        nifInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 9);
        });
    }
}

// Função para inicializar a sidebar
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        // Verifica o estado do sidebar
        if (localStorage.getItem('sidebarToggled') === 'true') {
            document.body.classList.add('sidebar-toggled');
        }

        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
            localStorage.setItem('sidebarToggled', document.body.classList.contains('sidebar-toggled'));
        });
    }
}

function loadUserData() {
    fetch('api/colaborador/dados.php', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    })
    .then(response => response.json())
    .then(data => {
        populateForm(data);
    })
    .catch(error => console.error('Erro ao carregar dados:', error));
}

function populateForm(data) {
    // Preencher campos do formulário
    document.getElementById('nome').value = data.nome;
    document.getElementById('email').value = data.email;
    document.getElementById('telefone').value = data.telefone;
    document.getElementById('morada').value = data.morada;
    document.getElementById('data_nascimento').value = data.data_nascimento;
    document.getElementById('nif').value = data.nif;
    document.getElementById('genero').value = data.genero;
    document.getElementById('data_entrada').value = data.data_entrada;
    document.getElementById('cargo').value = data.cargo;
    document.getElementById('departamento').value = data.departamento;
    document.getElementById('coordenador').value = data.coordenador;

    // Atualizar informações principais
    document.querySelector('.profile-info h1').textContent = data.nome;
    document.querySelector('.profile-info p').textContent = `${data.cargo} - ${data.departamento}`;
    document.querySelector('.status-badge').textContent = data.status;

    // Atualizar foto
    if (data.foto) {
        document.getElementById('foto-preview').innerHTML = `
            <img src="${data.foto}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
        `;
    }
}

function loadDocuments() {
    fetch('api/documentos/lista.php', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('documentos').innerHTML = createDocumentsList(data);
    })
    .catch(error => console.error('Erro ao carregar documentos:', error));
}

function createDocumentsList(documents) {
    return documents.map(doc => `
        <div class="document-item">
            <h3>${doc.nome}</h3>
            <p>Tipo: ${doc.tipo}</p>
            <p>Data de Validade: ${doc.data_validade}</p>
            <button onclick="downloadDocument('${doc.id}')">Baixar</button>
        </div>
    `).join('');
}

function loadBenefits() {
    fetch('api/beneficios/lista.php', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('beneficios').innerHTML = createBenefitsList(data);
    })
    .catch(error => console.error('Erro ao carregar benefícios:', error));
}

function createBenefitsList(benefits) {
    return benefits.map(benefit => `
        <div class="benefit-item">
            <h3>${benefit.nome}</h3>
            <p>Descrição: ${benefit.descricao}</p>
            <p>Status: ${benefit.status}</p>
        </div>
    `).join('');
}

// Função para atualizar o perfil
const profileForm = document.getElementById('profileForm');
if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar campos obrigatórios
        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefone = document.getElementById('telefone').value.trim();
        const morada = document.getElementById('morada').value.trim();
        const data_nascimento = document.getElementById('data_nascimento').value;
        const nif = document.getElementById('nif').value.trim();
        const genero = document.getElementById('genero').value;

        if (!nome || !email || !telefone || !morada || !data_nascimento || !nif || !genero) {
            showAlert('error', 'Por favor, preencha todos os campos obrigatórios');
            return;
        }

        // Validar formato do email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showAlert('error', 'Por favor, insira um email válido');
            return;
        }

        // Validar formato do NIF
        const nifRegex = /^[0-9]{9}$/;
        if (!nifRegex.test(nif)) {
            showAlert('error', 'Por favor, insira um NIF válido (9 dígitos)');
            return;
        }

        // Criar objeto com os dados do formulário
        const formData = new FormData(profileForm);
        const data = {
            nome: formData.get('nome'),
            email: formData.get('email'),
            telefone: formData.get('telefone'),
            morada: formData.get('morada'),
            nif: formData.get('nif'),
            dataNascimento: formData.get('data_nascimento'),
            codigoPostal: formData.get('codigo_postal'),
            localidade: formData.get('localidade'),
            observacoes: formData.get('observacoes')
        };

        // Enviar dados como JSON
        fetch('../DAL/colaborador.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar o header com as informações retornadas do servidor
                document.getElementById('displayName').textContent = data.data.nome;
                document.getElementById('displayEmail').textContent = data.data.email;
                document.getElementById('displayPhone').textContent = data.data.telefone;
                
                // Atualizar o nome no menu
                const menuName = document.querySelector('.menu-name');
                if (menuName) {
                    menuName.textContent = data.data.nome;
                }
                
                showAlert('success', 'Perfil atualizado com sucesso!');
                // Recarregar dados após atualização
                loadUserData();
            } else {
                showAlert('error', 'Erro ao atualizar perfil: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao atualizar perfil');
        });
    });
}

// Função para gerenciar o upload de foto
const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
if (uploadPhotoBtn) {
    uploadPhotoBtn.addEventListener('click', function() {
        const fotoInput = document.createElement('input');
        fotoInput.type = 'file';
        fotoInput.accept = 'image/*';
        fotoInput.style.display = 'none';
        
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamanho do arquivo (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('error', 'O arquivo deve ter no máximo 5MB');
                    return;
                }

                // Validar formato do arquivo
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    showAlert('error', 'Formato de arquivo não suportado. Por favor, use JPEG, PNG ou GIF.');
                    return;
                }

                // Pré-visualizar a foto
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.getElementById('foto-preview');
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" 
                             class="rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    `;
                };
                reader.readAsDataURL(file);

                // Enviar foto para o servidor
                const formData = new FormData();
                formData.append('foto', file);

                fetch('api/colaborador/upload_foto.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Foto atualizada com sucesso!');
                    } else {
                        showAlert('error', 'Erro ao atualizar foto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('error', 'Erro ao atualizar foto');
                });
            }
        });

        // Clicar no input de arquivo
        fotoInput.click();
    });
}

// Função para mostrar alertas
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const alertsContainer = document.getElementById('alertsContainer') || 
        document.querySelector('.profile-card');
    if (alertsContainer) {
        alertsContainer.insertBefore(alertDiv, alertsContainer.firstChild);
    }
    
    // Remover alerta após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
