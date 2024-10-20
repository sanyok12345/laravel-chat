Client.getMessages().then(messages => {
    const div = document.createElement('div');

    messages.forEach(message => {
        div.innerHTML = `<strong>${message.user_id}</strong>: ${message.message}`;
        document.querySelector('div').appendChild(div);
    });
});

const handleSendMessage = (event) => {
    const message = document.querySelector('input').value;

    Client.sendMessage(message).then(() => {
        document.querySelector('input').value = '';
    });
};