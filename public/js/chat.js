const user = {};
const localChatHistory = [];

const listenForMessages = () => {
    if (Client.isPolling) {
        return;
    }

    Client.isPolling = true;

    const poll = async () => {
        try {
            const messages = await Client.getLatestEvents();
            if (messages.length > 0) {
                updateLocalChatHistory(messages);
                renderMessages(messages);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setTimeout(poll, 250);
        }
    };

    poll();
};

const updateLocalChatHistory = (messages) => {
    messages.forEach(message => {
        const existingMessage = localChatHistory.find(m => m.id === message.id);
        if (!existingMessage) {
            localChatHistory.push({
                ...message,
                isOutgoing: message.user?.id === user.id
            });
        }
    });
};

const renderMessages = (messages) => {
    const messagesContainer = document.getElementById('messages');

    if (messagesContainer.classList.contains('loading')) {
        const loader = messagesContainer.getElementsByTagName('img')[0];
        if (loader) {
            loader.remove();
        }
        messagesContainer.classList.remove('loading');
    }

    messages.forEach(message => {
        const existingMessage = document.querySelector(`.chat-message[data-id="${message.id}"]`);

        if (!existingMessage) {
            const div = document.createElement('div');
            div.classList.add('chat-message');
            div.setAttribute('data-id', message.id);

            const isOutgoing = message.user?.id === user.id;
            div.classList.add(isOutgoing ? 'outgoing' : 'incoming');
            div.innerHTML = `
                <strong>${message.user?.name || "User"}</strong>
                ${message.reply_to_message ? `
                    <div class="reply">
                        <strong>${message.reply_to_message.user?.name || "User"}</strong>
                        ${message.reply_to_message.message}
                    </div>    
                ` : ''}
                ${message.message}
                <div class="time">${new Date(message.created_at).toLocaleTimeString()}</div>
            `;

            div.addEventListener('dblclick', () => showReplyMessage(message.id));

            messagesContainer.appendChild(div);
            scrollChatToBottom();
        }
    });
};

const handleSendMessage = async () => {
    const input = document.getElementById('text');
    const messageText = input.value.trim();

    if (messageText === '') return;

    if (messageText.length > 256) {
        alert('Message is too long');
        return;
    }

    const reply = document.getElementById('reply');
    const replyMessage = reply.state;
    const replyId = replyMessage?.id;
    hideReplyMessage();

    try {
        const newMessage = await Client.sendMessage(messageText, replyId);
        newMessage.user = user;
        newMessage.isOutgoing = true;

        input.value = '';
        updateLocalChatHistory([newMessage]);
        renderMessages([newMessage]);
        scrollChatToBottom();
    } catch (error) {
        console.error(error);
    }
};

const preloadProfile = async () => {
    const r = await Client.getProfile();
    user.id = r.id;
    user.name = r.name;
    user.email = r.email;
    user.nickname = r.nickname;
};

const loadMessagesFromAPI = async () => {
    try {
        const messages = await Client.getMessages();
        updateLocalChatHistory(messages);
        renderMessages(messages);
    } catch (error) {
        console.error('Ошибка при загрузке сообщений:', error);
    }
};

const scrollChatToBottom = () => {
    const messagesContainer = document.getElementById('messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
};

const showReplyMessage = (messageId) => {
    hideReplyMessage();

    const reply = document.getElementById('reply');
    const message = localChatHistory.find(m => m.id === messageId);
    if (message && message.message.trim() !== '') {
        const replyDiv = document.createElement('div');
        replyDiv.classList.add('reply');
        replyDiv.innerHTML = `
            <div class="user">
                ${message.user?.name || "User"}
            </div>
            <div class="message">
                ${message.message}
            </div>
        `;
        reply.style.display = 'flex';
        reply.state = message;

        replyDiv.addEventListener('click', hideReplyMessage);

        reply.appendChild(replyDiv);
        scrollChatToBottom();
    } else {
        hideReplyMessage();
    }
};

const hideReplyMessage = () => {
    const reply = document.getElementById('reply');
    reply.innerHTML = '';
    reply.style.display = 'none';
};

document.addEventListener('DOMContentLoaded', async () => {
    await preloadProfile();
    await loadMessagesFromAPI();
    listenForMessages();
    scrollChatToBottom();
});
