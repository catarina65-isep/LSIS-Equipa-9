<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

$page_title = "Ajuda - Tlantic";
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <style>
        /* Estilos do Chat */
        .main-content {
            margin-left: 0;
            padding: 20px;
            width: 100%;
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
                padding: 20px 30px;
            }
        }
        
        .chat-container {
            max-width: 100%;
            height: calc(100vh - 150px);
            min-height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            background: #fff;
            margin: 0 auto;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chat-header-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .chat-header-avatar i {
            font-size: 24px;
            color: white;
        }
        
        .chat-header-info h5 {
            margin: 0 0 2px 0;
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .chat-header-info p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #48bb78;
            border-radius: 50%;
            margin-right: 4px;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background-color: #f8fafc;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e2e8f0' fill-opacity='0.3'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .message {
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .bot { align-items: flex-start; }
        .user { align-items: flex-end; }
        
        .message-content {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            word-wrap: break-word;
            position: relative;
            line-height: 1.5;
            font-size: 0.9375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            animation: messageAppear 0.2s ease-out;
        }
        
        @keyframes messageAppear {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .bot .message-content {
            background-color: #fff;
            border-top-left-radius: 0.25rem;
            border: 1px solid #e2e8f0;
            color: #2d3748;
            margin-right: auto;
        }
        
        .user .message-content {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-top-right-radius: 0.25rem;
            margin-left: auto;
        }
        
        .message-time {
            font-size: 0.7rem;
            color: #718096;
            margin-top: 0.25rem;
            display: block;
            text-align: right;
        }
        
        .user .message-time {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .chat-input-container {
            padding: 1rem;
            background-color: #fff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
        }
        
        .chat-input {
            flex: 1;
            position: relative;
            margin-right: 0.75rem;
        }
        
        .chat-input input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 3rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            transition: all 0.2s;
            background-color: #f8fafc;
        }
        
        .chat-input input:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2);
            background-color: #fff;
        }
        
        .chat-input-actions {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 0.5rem;
        }
        
        .chat-input-actions button {
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        
        .chat-input-actions button:hover {
            color: #4e73df;
            background-color: #edf2f7;
        }
        
        .send-button {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .send-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .send-button:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Estilo para a barra de rolagem */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        
        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .typing-indicator {
            display: inline-flex;
            align-items: center;
            background: #fff;
            padding: 0.75rem 1rem;
            border-radius: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.3s ease-out;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: #a0aec0;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        /* Estilos para o Accordion de FAQ */
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #224abe;
            box-shadow: none;
        }
        
        .accordion-button:focus {
            border-color: #dee2e6;
            box-shadow: 0 0 0 0.25rem rgba(34, 74, 190, 0.1);
        }
        
        .accordion-button {
            font-weight: 500;
            padding: 1rem 1.25rem;
            color: #2d3748;
        }
        
        .accordion-button:after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232247ba'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transition: transform 0.2s ease-in-out;
        }
        
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232247ba'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        
        .accordion-body {
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #4a5568;
            background-color: #f8fafc;
        }
        
        .accordion-item {
            background-color: #fff;
            border: none;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        
        .accordion-item:last-child {
            border-bottom: none !important;
        }
        
        .card {
            border-radius: 0.75rem;
            overflow: hidden;
        }
        
        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include __DIR__ . '/includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm rounded">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Central de Ajuda</h1>
                        <p class="mb-0">Encontre respostas para suas dúvidas ou fale com nosso assistente virtual</p>
                    </div>
                </div>

                <!-- Conteúdo Principal -->
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Chat Container -->
                            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                                <div class="card-body p-0">
                                    <div class="chat-container">
                                        <div class="chat-header">
                                    <div class="chat-header-avatar">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="chat-header-info">
                                        <h5>Assistente Virtual LMM</h5>
                                        <p><span class="status-indicator"></span>Online - Digitando...</p>
                                    </div>
                                </div>
                                        
                                        <div class="chat-messages" id="chatMessages">
                                            <!-- Mensagem de boas-vindas -->
                                            <div class="message bot">
                                                <div class="message-content">
                                                    Olá! Eu sou o LMM, seu assistente virtual. Como posso te ajudar hoje?
                                                    <span class="message-time">Agora</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Mensagens serão adicionadas aqui via JavaScript -->
                                        </div>
                                        
                                        <div class="chat-input-container">
                                            <div class="chat-input">
                                                <input type="text" id="userInput" placeholder="Digite sua mensagem..." autocomplete="off" onkeypress="if(event.key === 'Enter') sendMessage()">
                                                <div class="chat-input-actions">
                                                    <button type="button" title="Anexar arquivo">
                                                        <i class="fas fa-paperclip"></i>
                                                    </button>
                                                    <button type="button" title="Enviar mensagem de voz">
                                                        <i class="fas fa-microphone"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <button class="send-button" onclick="sendMessage()">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coluna de Perguntas Frequentes -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="mb-0 d-flex align-items-center">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        Perguntas Frequentes
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="accordion accordion-flush" id="faqAccordion">
                                        <!-- Pergunta 1 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                                    Como adiciono um novo utilizador?
                                                </button>
                                            </h3>
                                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Vá a "Utilizadores" > "Adicionar Utilizador". Preencha os campos obrigatórios e clique em "Guardar".
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 2 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                                    Como redefino uma palavra-passe?
                                                </button>
                                            </h3>
                                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Na lista de utilizadores, clique no ícone de edição e selecione "Redefinir Palavra-passe".
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 3 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                                    Como crio um campo personalizado?
                                                </button>
                                            </h3>
                                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Vá a "Campos Personalizados" > "Adicionar Campo". Escolha o tipo e preencha as informações necessárias.
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 4 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                                    Como gero um relatório?
                                                </button>
                                            </h3>
                                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Acesse a secção "Relatórios", selecione o tipo desejado e aplique os filtros necessários.
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 5 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                                    Como altero o meu perfil?
                                                </button>
                                            </h3>
                                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Clique na sua foto de perfil no canto superior direito e selecione "O Meu Perfil".
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 6 -->
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                                    Como exporto dados para Excel?
                                                </button>
                                            </h3>
                                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Na maioria das listagens, há um botão "Exportar" no canto superior direito.
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Pergunta 7 -->
                                        <div class="accordion-item">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                                    Como contacto o suporte técnico?
                                                </button>
                                            </h3>
                                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Utilize o chat de suporte ou envie um e-mail para suporte@exemplo.pt
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Função para formatar a data/hora
        function formatDateTime(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'Agora';
            if (diffMins < 60) return `Há ${diffMins} min${diffMins > 1 ? 's' : ''}`;
            
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            return `Hoje, ${hours}:${minutes}`;
        }
        
        // Função para mostrar indicador de digitação
        function showTypingIndicator() {
            const chatMessages = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot';
            typingDiv.id = 'typingIndicator';
            typingDiv.style.opacity = '0';
            typingDiv.style.transform = 'translateY(10px)';
            typingDiv.style.transition = 'opacity 0.2s, transform 0.2s';
            
            const typingContent = document.createElement('div');
            typingContent.className = 'typing-indicator';
            typingContent.innerHTML = `
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            `;
            
            typingDiv.appendChild(typingContent);
            chatMessages.appendChild(typingDiv);
            
            // Forçar reflow
            void typingDiv.offsetWidth;
            
            typingDiv.style.opacity = '1';
            typingDiv.style.transform = 'translateY(0)';
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return typingDiv;
        }
        
        // Função para esconder indicador de digitação
        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.style.opacity = '0';
                typingIndicator.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    typingIndicator.remove();
                }, 200);
            }
        }
        
        // Função para adicionar mensagem ao chat
        function addMessage(sender, text) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateY(10px)';
            messageDiv.style.transition = 'opacity 0.3s, transform 0.3s';
            
            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            messageContent.innerHTML = text;
            
            const messageTime = document.createElement('span');
            messageTime.className = 'message-time';
            messageTime.textContent = formatDateTime(new Date());
            
            messageContent.appendChild(messageTime);
            messageDiv.appendChild(messageContent);
            
            chatMessages.appendChild(messageDiv);
            
            // Animar entrada da mensagem
            setTimeout(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
            }, 10);
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Função para enviar mensagem
        async function sendMessage() {
            const userInput = document.getElementById('userInput');
            const message = userInput.value.trim();
            
            if (message === '') return;
            
            // Desabilitar input temporariamente
            userInput.disabled = true;
            
            // Adicionar mensagem do usuário
            addMessage('user', message);
            userInput.value = '';
            
            // Mostrar indicador de digitação
            const typingIndicator = showTypingIndicator();
            
            try {
                // Simular atraso de resposta
                await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 1000));
                
                // Obter resposta do bot
                const botResponse = getBotResponse(message);
                
                // Esconder indicador de digitação e mostrar resposta
                hideTypingIndicator();
                
                // Adicionar pequeno atraso antes de mostrar a resposta
                await new Promise(resolve => setTimeout(resolve, 300));
                addMessage('bot', botResponse);
                
            } catch (error) {
                console.error('Erro ao processar mensagem:', error);
                hideTypingIndicator();
                addMessage('bot', 'Desculpe, ocorreu um erro ao processar sua mensagem. Por favor, tente novamente mais tarde.');
            } finally {
                // Reabilitar input
                userInput.disabled = false;
                userInput.focus();
            }
        }
        
        // Base de conhecimento do assistente
        const knowledgeBase = {
            // Saudações
            'saudacao': {
                patterns: ['olá', 'ola', 'oi', 'eae', 'e aí', 'bom dia', 'boa tarde', 'boa noite', 'olá', 'oi bot', 'oi lmm', 'e aí lmm'],
                responses: [
                    'Olá! Sou o LMM, seu assistente virtual. Como posso te ajudar hoje?',
                    'Oi! Estou aqui para te ajudar. Em que posso ser útil?',
                    'Olá! Como posso te auxiliar hoje?',
                    'Oi! Estou à disposição para ajudar com suas dúvidas sobre o sistema.'
                ]
            },
            // Adicionar usuário
            'adicionar_usuario': {
                patterns: [
                    'adicionar usuário', 'criar usuário', 'novo usuário', 'como adiciono um usuário',
                    'como criar usuário', 'cadastrar usuário', 'inserir usuário', 'registrar usuário'
                ],
                responses: [
                    'Para adicionar um novo usuário, siga estes passos:\n1. Acesse o menu "Usuários"\n2. Clique em "Adicionar Usuário"\n3. Preencha os campos obrigatórios (nome, e-mail e perfil)\n4. Clique em "Salvar"\n\nDica: Você pode definir uma senha temporária ou optar por enviar um link de ativação por e-mail.',
                    'Para criar um novo usuário, vá até o menu lateral em "Usuários" e selecione "Adicionar Usuário". Lembre-se de que é necessário ter permissão de administrador para esta ação.'
                ]
            },
            // Redefinir senha
            'redefinir_senha': {
                patterns: [
                    'redefinir senha', 'esqueci a senha', 'mudar senha', 'alterar senha',
                    'como mudo a senha', 'como altero a senha', 'senha esquecida', 'bloqueou a senha'
                ],
                responses: [
                    'Para redefinir uma senha, siga estes passos:\n1. Acesse a lista de usuários\n2. Localize o usuário desejado\n3. Clique no ícone de edição (lápis)\n4. Selecione "Redefinir Senha"\n5. Um e-mail será enviado com as instruções para criar uma nova senha.',
                    'Se esqueceu sua senha, você pode:\n1. Solicitar redefinição na página de login clicando em "Esqueceu a senha?"\n2. Ou, se for administrador, redefinir a senha de qualquer usuário através do painel de administração.'
                ]
            },
            // Campos personalizados
            'campos_personalizados': {
                patterns: [
                    'criar campo personalizado', 'adicionar campo personalizado', 'como criar campo',
                    'novo campo personalizado', 'campos personalizados', 'como adicionar campo',
                    'gerenciar campos personalizados'
                ],
                responses: [
                    'Para criar um campo personalizado, siga estes passos:\n1. Acesse "Campos Personalizados" no menu\n2. Clique em "Adicionar Campo"\n3. Preencha as informações:\n   - Nome do campo\n   - Tipo (texto, número, data, seleção, etc.)\n   - Se é obrigatório\n   - Opções (se for seleção múltipla ou lista)\n4. Clique em "Salvar"\n\nDica: Os campos personalizados permitem adicionar informações extras aos seus registros de forma flexível.',
                    'Os campos personalizados permitem estender os formulários do sistema. Você pode criar campos de diferentes tipos, como texto, número, data, seleção única ou múltipla, e muito mais. Acesse a seção "Campos Personalizados" no menu para começar.'
                ]
            },
            // Relatórios
            'relatorios': {
                patterns: [
                    'gerar relatório', 'como faço um relatório', 'exportar relatório',
                    'relatórios', 'como ver relatórios', 'criar relatório', 'exportar dados'
                ],
                responses: [
                    'Para gerar um relatório, siga estes passos:\n1. Acesse a seção "Relatórios" no menu\n2. Selecione o tipo de relatório desejado\n3. Aplique os filtros necessários\n4. Clique em "Gerar Relatório"\n5. Opcionalmente, exporte para Excel, PDF ou outro formato disponível.\n\nDica: Você pode salvar os filtros usados frequentemente como modelos para uso futuro.',
                    'O sistema oferece diferentes tipos de relatórios que podem ser personalizados com filtros específicos. Após gerar um relatório, você pode exportá-lo em vários formatos como Excel, PDF ou CSV para análise posterior.'
                ]
            },
            // Perfil do usuário
            'perfil_usuario': {
                patterns: [
                    'alterar meu perfil', 'editar perfil', 'meus dados', 'atualizar perfil',
                    'mudar email', 'alterar minha senha', 'meu perfil'
                ],
                responses: [
                    'Para atualizar seu perfil, clique na sua foto no canto superior direito e selecione "Meu Perfil". Lá você pode:\n\n- Atualizar suas informações pessoais\n- Alterar sua foto de perfil\n- Mudar sua senha\n- Atualizar preferências de notificação\n\nLembre-se de clicar em "Salvar" após fazer as alterações.',
                    'Você pode gerenciar suas informações pessoais acessando o menu do seu perfil no canto superior direito. Lá estão disponíveis opções para atualizar seus dados, foto e configurações de privacidade.'
                ]
            },
            // Exportar dados
            'exportar_dados': {
                patterns: [
                    'exportar para excel', 'exportar dados', 'baixar planilha', 'exportar tabela',
                    'como exportar para excel', 'exportar para csv', 'baixar relatório'
                ],
                responses: [
                    'Para exportar dados para Excel ou outro formato, siga estes passos:\n1. Na tela de listagem desejada, localize o botão "Exportar"\n2. Selecione o formato desejado (Excel, CSV, PDF, etc.)\n3. Escolha as colunas a serem incluídas (quando aplicável)\n4. Clique em "Exportar"\n\nDica: Algumas telas permitem aplicar filtros antes de exportar, para que você obtenha exatamente os dados que precisa.',
                    'A exportação de dados está disponível na maioria das telas de listagem. Procure pelo ícone de download ou pelo botão "Exportar". Você pode escolher entre diferentes formatos dependendo da sua necessidade.'
                ]
            },
            // Suporte técnico
            'suporte': {
                patterns: [
                    'suporte técnico', 'contatar suporte', 'ajuda', 'preciso de ajuda',
                    'problema com o sistema', 'reportar erro', 'falar com suporte', 'contato suporte'
                ],
                responses: [
                    'Para entrar em contato com o suporte técnico, você pode:\n\n1. Enviar um e-mail para suporte@exemplo.pt\n2. Ligar para o número (XX) XXXX-XXXX\n3. Utilizar o chat de suporte no canto inferior direito da tela\n\nHorário de atendimento: Segunda a Sexta, das 8h às 18h.\n\nPor favor, descreva detalhadamente o problema encontrado para que possamos ajudá-lo melhor.',
                    'Se você está enfrentando problemas ou tem dúvidas, nossa equipe de suporte está à disposição. Você pode nos contatar por e-mail, telefone ou diretamente pelo chat. Não se esqueça de informar:\n- O que você estava tentando fazer\n- Qual mensagem de erro apareceu (se houver)\n- O navegador e sistema operacional que está usando'
                ]
            },
            // Agradecimento
            'agradecimento': {
                patterns: [
                    'obrigado', 'obrigada', 'valeu', 'agradeço', 'agradecido', 'agradecida',
                    'muito obrigado', 'muito obrigada', 'obrigadão', 'obrigadinha', 'obrigadinho'
                ],
                responses: [
                    'De nada! Estou aqui para ajudar. Se precisar de mais alguma coisa, é só chamar!',
                    'Disponha! Fico feliz em poder ajudar. Volte sempre que precisar!',
                    'Por nada! Se tiver mais dúvidas, estou à disposição.',
                    'Foi um prazer ajudar! Se precisar de mais alguma coisa, é só chamar.'
                ]
            },
            // Despedida
            'despedida': {
                patterns: [
                    'tchau', 'até mais', 'até logo', 'até breve', 'até depois', 'adeus',
                    'flw', 'falou', 'vou embora', 'até a próxima', 'xau', 'tchauzinho'
                ],
                responses: [
                    'Até mais! Estarei aqui se precisar de ajuda.',
                    'Até logo! Volte sempre que tiver dúvidas.',
                    'Tchau! Tenha um ótimo dia!',
                    'Até a próxima! Estou à disposição para ajudar.'
                ]
            },
            // Padrão (quando não entende a pergunta)
            'padrao': {
                responses: [
                    'Desculpe, não entendi completamente. Poderia reformular sua pergunta?',
                    'Ainda estou aprendendo. Poderia tentar de outra forma?',
                    'Não tenho certeza se entendi. Você poderia explicar de outra maneira?',
                    'No momento, posso ajudar com dúvidas sobre: adicionar usuários, redefinir senhas, criar campos personalizados, visualizar relatórios e configurações do sistema. Sobre o que você gostaria de saber?',
                    'Parece que não entendi completamente. Você poderia tentar de outra forma? Posso ajudar com dúvidas sobre o sistema, usuários, relatórios e configurações.'
                ]
            }
        };

        // Função para obter resposta do bot
        function getBotResponse(message) {
            const lowerMessage = message.toLowerCase().trim();
            
            // Verificar padrões em cada categoria
            for (const [category, data] of Object.entries(knowledgeBase)) {
                if (category !== 'padrao') {
                    for (const pattern of data.patterns) {
                        if (lowerMessage.includes(pattern)) {
                            const responses = data.responses;
                            return responses[Math.floor(Math.random() * responses.length)];
                        }
                    }
                }
            }
            
            // Se não encontrou padrão, verificar por saudações básicas
            if (/^(oi|olá|ola|eae|e aí|bom dia|boa tarde|boa noite)/i.test(lowerMessage)) {
                const responses = knowledgeBase.saudacao.responses;
                return responses[Math.floor(Math.random() * responses.length)];
            }
            
            // Se não encontrou nada, retornar resposta padrão
            const defaultResponses = knowledgeBase.padrao.responses;
            return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
        }
        
        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            const userInput = document.getElementById('userInput');
            
            // Enviar mensagem ao pressionar Enter (sem Shift)
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Auto-ajustar altura do input
            userInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Focar no input quando a página carregar
            userInput.focus();
        });
    </script>
</body>
</html>
