Client.getMessages().then(messages => {
    const messagesContainer = document.getElementById('messages');
    
    messagesContainer.classList.remove('loading');
    messagesContainer.innerHTML = '';
    messages.forEach(message => {
        const div = document.createElement('div');
        div.classList.add('chat-message');
        div.innerHTML = `
            <strong>${message.user_id}</strong>
            ${message.message}
        `;
        messagesContainer.appendChild(div);
    });
});

const handleSendMessage = () => {
    const input = document.getElementById('text');
    const messagesContainer = document.getElementById('messages');

    Client.sendMessage(input.value).then(() => {
        const div = document.createElement('div');
        div.classList.add('chat-message');
        div.innerHTML = `<strong>You</strong>: ${input.value}`;
        messagesContainer.appendChild(div);
        input.value = '';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    });
};