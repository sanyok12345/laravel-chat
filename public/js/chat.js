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
            if (messages.length >= 0) {
                updateLocalChatHistory(messages);
                renderMessages(messages);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setTimeout(poll, 2000);
        }
    };

    poll();
};

const updateLocalChatHistory = (messages) => {
    messages
    .sort((a, b) => b.id - a.id)
    .forEach(message => {
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
                ${message.message}
            `;
            messagesContainer.appendChild(div);

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
};

const handleSendMessage = async () => {
    const input = document.getElementById('text');
    const messageText = input.value.trim();

    if (messageText === '') return;

    try {
        const newMessage = await Client.sendMessage(messageText);
        newMessage.user = user;
        newMessage.isOutgoing = true;

        input.value = '';
        updateLocalChatHistory([newMessage]);
        renderMessages([newMessage]);
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

document.addEventListener('DOMContentLoaded', async () => {
    await preloadProfile();
    listenForMessages();

    const form = document.getElementById('form');
    form.addEventListener('submit', event => {
        event.preventDefault();
        handleSendMessage();
    });
});