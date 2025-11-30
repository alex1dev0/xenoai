document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.querySelector('.chat-container');
    const messagesDiv = document.getElementById('messages');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const scrollButton = document.getElementById('scroll-button');
    const resetButton = document.getElementById('reset-button');
    const sidebar = document.getElementById('sidebar');
    const newChatBtn = document.getElementById('new-chat-btn');
    const chatList = document.getElementById('chat-list');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    
    let currentConversationId = null;
    let autoScrollEnabled = true;
    let isTyping = false;
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;

    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const icon = toggleSidebarBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
                toggleSidebarBtn.title = "Espandi menu";
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
                toggleSidebarBtn.title = "Riduci menu";
            }
        });
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
    }

    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, {passive: true});

    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, {passive: true});

    function handleSwipe() {
        const swipeThreshold = 70;
        const verticalThreshold = 50;
        
        const horizontalDistance = Math.abs(touchEndX - touchStartX);
        const verticalDistance = Math.abs(touchEndY - touchStartY);

        if (verticalDistance > verticalThreshold) return;
        
        if (touchEndX > touchStartX + swipeThreshold && touchStartX < 60) {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('mobile-open');
            }
        }
        
        if (touchStartX > touchEndX + swipeThreshold) {
            if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
            }
        }
    }
    
    newChatBtn.addEventListener('click', startNewChat);

    function startNewChat() {
        userInput.value = '';
        userInput.style.height = 'auto';
        currentConversationId = null;
        messagesDiv.innerHTML = '';
        isTyping = false;
        userInput.disabled = false;
        sendButton.disabled = false;
        userInput.focus();
        
        addMessage('Ciao! Come posso aiutarti oggi?', 'ai', false);
        updateActiveChat(null);
        
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-open');
        }
    }

    function loadChatList() {
        fetch('api/proxy.php?type=manager&action=list', {
             method: 'POST' 
        })
            .then(res => res.json())
            .then(data => {
                renderChatList(data.conversations);
            })
            .catch(err => console.error('Error loading chats:', err));
    }

    function renderChatList(conversations) {
        chatList.innerHTML = '';
        conversations.forEach(chat => {
            const li = document.createElement('li');
            li.className = 'chat-item';
            if (chat.id === currentConversationId) li.classList.add('active');
            
            li.innerHTML = `
                <div class="chat-item-content">
                    <i class="fas fa-message"></i>
                    <span class="chat-title">${escapeHtml(chat.title)}</span>
                </div>
                <button class="delete-chat-btn" title="Elimina chat">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            const contentDiv = li.querySelector('.chat-item-content');
            contentDiv.addEventListener('click', () => loadChat(chat.id));

            const deleteBtn = li.querySelector('.delete-chat-btn');
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                deleteChat(chat.id);
            });
            
            chatList.appendChild(li);
        });
    }
    
    function deleteChat(id) {
        chatToDeleteId = id;
        modalOverlay.classList.remove('hidden');
    }

    function loadChat(id) {
        if (isTyping) return;
        
        currentConversationId = id;
        updateActiveChat(id);
        
        messagesDiv.innerHTML = '';
        
        fetch(`api/proxy.php?type=manager&action=get&id=${id}`, {
             method: 'POST'
        })
            .then(res => res.json())
            .then(data => {
                if (data.messages) {
                    data.messages.forEach(msg => {
                        addMessage(msg.content, msg.role === 'user' ? 'user' : 'ai', false);
                    });
                    scrollToBottom();
                }
            })
            .catch(err => console.error('Error loading chat:', err));
            
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-open');
        }
    }

    function updateActiveChat(id) {
        const items = chatList.querySelectorAll('.chat-item');
        items.forEach(item => {
            item.classList.remove('active');
        });
        loadChatList(); 
    }

    scrollButton.addEventListener('click', function() {
        scrollToBottom();
        autoScrollEnabled = true;
        checkScrollPosition();
    });

    function checkScrollPosition() {
        const isScrolledToBottom = messagesContainer.scrollHeight - messagesContainer.clientHeight <= messagesContainer.scrollTop + 50;
        if (isScrolledToBottom) {
            scrollButton.classList.remove('visible');
            autoScrollEnabled = true;
        } else {
            scrollButton.classList.add('visible');
            autoScrollEnabled = false;
        }
    }

    messagesContainer.addEventListener('scroll', checkScrollPosition);

    userInput.addEventListener('input', () => {
        userInput.style.height = 'auto';
        userInput.style.height = (userInput.scrollHeight) + 'px';
    });

    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendButton.addEventListener('click', sendMessage);

    const modalOverlay = document.getElementById('modal-overlay');
    const confirmResetBtn = document.getElementById('confirm-reset');
    const cancelResetBtn = document.getElementById('cancel-reset');
    let chatToDeleteId = null;

    resetButton.addEventListener('click', () => {
        if (currentConversationId) {
            chatToDeleteId = currentConversationId;
            modalOverlay.classList.remove('hidden');
        } else {
            startNewChat();
        }
    });

    cancelResetBtn.addEventListener('click', () => {
        modalOverlay.classList.add('hidden');
        chatToDeleteId = null;
    });

    confirmResetBtn.addEventListener('click', () => {
        modalOverlay.classList.add('hidden');
        if (chatToDeleteId) {
            deleteChatRequest(chatToDeleteId);
        }
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.add('hidden');
            chatToDeleteId = null;
        }
    });

    function deleteChatRequest(id) {
        fetch('api/proxy.php?type=manager&action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (currentConversationId === id) {
                    startNewChat();
                }
                loadChatList();
            }
            chatToDeleteId = null;
        })
        .catch(err => {
            console.error('Error deleting chat:', err);
            chatToDeleteId = null;
        });
    }

    function sendMessage() {
        const message = userInput.value.trim();
        if (!message) return;

        if (handleSpecialQueries(message)) return;

        userInput.disabled = true;
        sendButton.disabled = true;
        isTyping = true;

        addMessage(message, 'user');

        userInput.value = '';
        userInput.style.height = 'auto';
        updateStatus('thinking');
        showTypingIndicator();

        let aiMessageDiv;
        let aiMessageContent;

        fetch('api/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                message: message,
                conversation_id: currentConversationId
            })
        })
        .then(async response => {
            if (!response.ok) throw new Error('Errore nella risposta del server');
            
            removeTypingIndicator();
            aiMessageDiv = addMessage('', 'ai', false);
            aiMessageContent = aiMessageDiv.querySelector('.message-content');
            
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let aiResponseText = '';
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                
                const chunk = decoder.decode(value, {stream: true});
                buffer += chunk;
                
                const lines = buffer.split('\n\n');
                buffer = lines.pop();
                
                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const dataStr = line.substring(6);
                        try {
                            const data = JSON.parse(dataStr);
                            
                            if (data.type === 'meta') {
                                if (data.conversation_id) {
                                    currentConversationId = data.conversation_id;
                                    if (data.new_chat) {
                                        loadChatList();
                                    }
                                }
                            } else if (data.type === 'chunk') {
                                aiResponseText += data.content;
                                aiMessageContent.innerHTML = formatMessage(aiResponseText);
                                if (autoScrollEnabled) scrollToBottom();
                            } else if (data.type === 'done') {
                                updateStatus('ready');
                                userInput.disabled = false;
                                sendButton.disabled = false;
                                userInput.focus();
                                isTyping = false;
                            } else if (data.type === 'error') {
                                console.error('Error from server:', data.error);
                                addMessage('Errore: ' + data.error, 'ai');
                            }
                        } catch (e) {
                            console.error('Error parsing SSE data:', e);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            removeTypingIndicator();
            addMessage('Mi dispiace, si è verificato un errore. Riprova più tardi.', 'ai');
            updateStatus('error');
            userInput.disabled = false;
            sendButton.disabled = false;
            userInput.focus();
            isTyping = false;
        });
    }

    function handleSpecialQueries(message) {
        const lowerMsg = message.toLowerCase();
        if (lowerMsg.includes('da chi sei stato creato') || 
            lowerMsg.includes('chi ti ha creato') || 
            lowerMsg.includes('chi ti ha sviluppato')) {
            
            addMessage(message, 'user');
            userInput.value = '';
            setTimeout(() => {
                addMessage('Sono stato creato da Alex1Dev usando il modello gratuito di Ollama.', 'ai');
            }, 500);
            return true;
        }
        return false;
    }

    function addMessage(content, sender, animate = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        if (!animate && sender === 'ai') {
            messageContent.innerHTML = formatMessage(content);
        } else if (sender === 'user') {
            messageContent.innerHTML = formatMessage(content);
        }
        
        messageDiv.appendChild(messageContent);
        messagesDiv.appendChild(messageDiv);
        
        if (autoScrollEnabled) scrollToBottom();
        
        return messageDiv;
    }

    function scrollToBottom() {
        messagesContainer.scrollTo({
            top: messagesContainer.scrollHeight,
            behavior: 'smooth'
        });
        scrollButton.classList.remove('visible');
        autoScrollEnabled = true;
    }

    function formatMessage(text) {
        let formatted = escapeHtml(text);
        formatted = formatted.replace(/\n/g, '<br>');
        formatted = formatted.replace(/```([\s\S]*?)```/g, (match, code) => '<pre><code>' + code + '</code></pre>');
        formatted = formatted.replace(/`([^`]+)`/g, (match, code) => '<code>' + code + '</code>');
        formatted = formatted.replace(/\*\*([^\*]+)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\[([^\]]+)\]\(([^\)]+)\)/g, '<a href="$2" target="_blank">$1</a>');
        return formatted;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message';
        typingDiv.id = 'typing-indicator';
        
        const typingContent = document.createElement('div');
        typingContent.className = 'message-content typing-indicator';
        
        const typingDots = document.createElement('div');
        typingDots.className = 'typing-dots';
        
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('span');
            typingDots.appendChild(dot);
        }
        
        typingContent.appendChild(typingDots);
        typingDiv.appendChild(typingContent);
        messagesDiv.appendChild(typingDiv);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
    }

    function updateStatus(status) {
        const indicator = document.querySelector('.status-indicator');
        indicator.className = 'status-indicator ' + status;
    }

    loadChatList();
});
