class ChatAPI {
    static user = {};
    static localChatHistory = [];

    static async preloadProfile() {
        const profile = await Client.getProfile();
        Object.assign(ChatAPI.user, profile);
    }

    static async loadMessagesFromAPI() {
        try {
            const messages = await Client.getMessages();
            ChatAPI.updateLocalChatHistory(messages);
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    static async sendMessage(messageText, replyMessageId) {
        try {
            const newMessage = await Client.sendMessage(messageText, replyMessageId);
            newMessage.user = ChatAPI.user;
            newMessage.isOutgoing = true;
            ChatAPI.localChatHistory.push(newMessage);
            return newMessage;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    static async editMessage(messageId, newText) {
        try {
            await Client.editMessage(messageId, newText);
            const message = ChatAPI.localChatHistory.find(m => m.id === messageId);
            if (message) {
                message.message = newText;
            }
        } catch (error) {
            console.error('Error editing message:', error);
            throw error;
        }
    }

    static async deleteMessage(messageId) {
        try {
            await Client.deleteMessage(messageId);
            const index = ChatAPI.localChatHistory.findIndex(m => m.id === messageId);
            if (index !== -1) {
                ChatAPI.localChatHistory.splice(index, 1);
            }
        } catch (error) {
            console.error('Error deleting message:', error);
            throw error;
        }
    }

    static updateLocalChatHistory(messages) {
        messages.forEach(message => {
            const existingMessage = ChatAPI.localChatHistory.find(m => m.id === message.id);
            if (!existingMessage) {
                ChatAPI.localChatHistory.push({
                    ...message,
                    isOutgoing: message.user?.id === ChatAPI.user.id
                });
            } else {
                existingMessage.message = message.message;
            }
        });
    }

    static async listenForMessages(callback) {
        if (Client.isPolling) return;

        Client.isPolling = true;

        const poll = async () => {
            try {
                const messages = await Client.getLatestEvents();
                if (messages.length > 0) {
                    ChatAPI.updateLocalChatHistory(messages);
                    callback(ChatAPI.localChatHistory);
                }
                const visibleMessages = ChatUI.getVisibleMessages();
                if (visibleMessages.length > 0) {
                    const updatedMessages = await Client.getMessagesByIds(visibleMessages.map(m => m.id));
                    ChatAPI.updateLocalChatHistory(updatedMessages);
                    callback(ChatAPI.localChatHistory);
                }
            } catch (error) {
                console.error(error);
            } finally {
                setTimeout(poll, 250);
            }
        };

        poll();
    }
}

class ChatUI {
    static selectedMessageId = null;
    static replyMessageId = null;

    static initEventListeners() {
        document.getElementById('edit').addEventListener('click', ChatUI.handleEditMessage);
        document.getElementById('remove').addEventListener('click', ChatUI.handleRemoveMessage);
        document.addEventListener('DOMContentLoaded', ChatUI.initChat);
    }

    static async initChat() {
        await ChatAPI.preloadProfile();
        await ChatAPI.loadMessagesFromAPI();
        ChatAPI.listenForMessages(ChatUI.renderMessages);
        ChatUI.scrollChatToBottom();
    }

    static renderMessages(messages) {
        const messagesContainer = document.getElementById('messages');
        let shouldScroll = false;

        messages.sort((a, b) => a.id - b.id);

        messages.forEach(message => {
            const existingMessageDiv = document.querySelector(`[data-id="${message.id}"]`);
            if (existingMessageDiv) {
                ChatUI.updateMessageElement(existingMessageDiv, message);
            } else {
                const messageDiv = ChatUI.createMessageElement(message);
                if (messageDiv) {
                    messagesContainer.appendChild(messageDiv);
                    shouldScroll = true;
                }
            }
        });

        if (shouldScroll) {
            ChatUI.scrollChatToBottom();
        }
    }

    static createMessageElement(message) {
        const div = document.createElement('div');
        const isOutgoing = message.user?.id === ChatAPI.user.id;

        div.classList.add('chat-message', isOutgoing ? 'outgoing' : 'incoming');
        div.setAttribute('data-id', message.id);
        div.innerHTML = `
            <strong>
                ${message.user?.name || "User"}
            </strong>
            ${message.reply_to_message ? `
                <div class="reply">
                    <strong>
                        ${message.reply_to_message.user?.name || "User"}
                    </strong>
                    ${message.reply_to_message.message}
                </div>
            ` : ''}
            ${message.message}
            <div class="time">
                ${new Date(message.created_at).toLocaleTimeString()}
            </div>
        `;

        div.addEventListener('dblclick', () => ChatUI.showReplyMessage(message.id));
        div.addEventListener('contextmenu', (event) => ChatUI.showContextMenu(message.id, isOutgoing, event));

        return div;
    }

    static updateMessageElement(element, message) {
        const isOutgoing = message.user?.id === ChatAPI.user.id;

        element.classList.add('chat-message', isOutgoing ? 'outgoing' : 'incoming');
        element.setAttribute('data-id', message.id);
        element.innerHTML = `
            <strong>
                ${message.user?.name || "User"}
            </strong>
            ${message.reply_to_message ? `
                <div class="reply">
                    <strong>
                        ${message.reply_to_message.user?.name || "User"}
                    </strong>
                    ${message.reply_to_message.message}
                </div>
            ` : ''}
            ${message.message}
            <div class="time">
                ${new Date(message.created_at).toLocaleTimeString()}
            </div>
        `;
    }

    static async handleSendMessage() {
        const input = document.getElementById('text');
        const messageText = input.value.trim();
        const editMessageId = document.getElementById('edit-message-id').value;

        if (messageText === '' || messageText.length > 256) return;

        if (editMessageId) {
            await ChatUI.confirmEditMessage();
        } else {
            try {
                const newMessage = await ChatAPI.sendMessage(messageText, ChatUI.replyMessageId);
                ChatUI.renderMessages(ChatAPI.localChatHistory);
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }

        input.value = '';
        ChatUI.clearInfoPanel();
    }

    static async confirmEditMessage() {
        const messageId = document.getElementById('edit-message-id').value;
        const newText = document.getElementById('text').value;

        try {
            await ChatAPI.editMessage(messageId, newText);
            ChatUI.renderMessages(ChatAPI.localChatHistory);
            ChatUI.clearInfoPanel();
        } catch (error) {
            console.error('Error editing message:', error);
        }
    }

    static async handleEditMessage() {
        const message = ChatAPI.localChatHistory.find(m => m.id === ChatUI.selectedMessageId);
        if (message) {
            document.getElementById('text').value = message.message;
            document.getElementById('edit-message-id').value = ChatUI.selectedMessageId;
            ChatUI.updateInfoPanel('Editing message', message.message);
        }
        ChatUI.hideContextMenu();
    }

    static async handleRemoveMessage() {
        try {
            await ChatAPI.deleteMessage(ChatUI.selectedMessageId);
            ChatUI.removeMessageElement(ChatUI.selectedMessageId);
        } catch (error) {
            console.error('Error deleting message:', error);
        }
        ChatUI.hideContextMenu();
    }

    static removeMessageElement(messageId) {
        const messageElement = document.querySelector(`[data-id="${messageId}"]`);
        if (messageElement) {
            messageElement.remove();
        }
    }

    static showContextMenu(messageId, isOutgoing, event) {
        if (!isOutgoing) return;

        ChatUI.selectedMessageId = messageId;
        const contextMenu = document.getElementById('context-menu');
        contextMenu.style.display = 'block';
        contextMenu.style.top = `${event.clientY}px`;
        contextMenu.style.left = `${event.clientX}px`;
        document.addEventListener('click', ChatUI.hideContextMenu);
        event.preventDefault();
    }

    static hideContextMenu() {
        const contextMenu = document.getElementById('context-menu');
        contextMenu.style.display = 'none';
        document.removeEventListener('click', ChatUI.hideContextMenu);
    }

    static scrollChatToBottom() {
        const messagesContainer = document.getElementById('messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    static clearInfoPanel() {
        const infoPanel = document.getElementById('info-panel');
        infoPanel.style.display = 'none';
        document.getElementById('edit-message-id').value = '';
        document.getElementById('text').value = '';
    }

    static updateInfoPanel(action, message) {
        const infoPanel = document.getElementById('info-panel');
        const infoText = infoPanel.querySelector('.info-text');
        const infoMessage = infoPanel.querySelector('.info-message');
        infoText.textContent = action;
        infoMessage.textContent = `${message}`;
        infoPanel.style.display = 'flex';
    }

    static showReplyMessage(messageId) {
        const message = ChatAPI.localChatHistory.find(m => m.id === messageId);
        if (message && message.message.trim() !== '') {
            ChatUI.replyMessageId = messageId;
            ChatUI.updateInfoPanel('Replying to message', message.message);
        }
    }

    static getVisibleMessages() {
        const messagesContainer = document.getElementById('messages');
        const messages = Array.from(messagesContainer.getElementsByClassName('chat-message'));
        const visibleMessages = messages.filter(message => {
            const rect = message.getBoundingClientRect();
            return rect.top >= 0 && rect.bottom <= window.innerHeight;
        });
        return visibleMessages.map(message => ({
            id: parseInt(message.getAttribute('data-id')),
            element: message
        }));
    }
}

ChatUI.initEventListeners();