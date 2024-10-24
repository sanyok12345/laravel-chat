class API {
    static token() {
        const token = document.querySelector('meta[name="api-token"]');
        return token ? token.content : '';
    }

    static call(method, url, data, callback) {
        if (typeof callback !== 'function') {
            return new Promise((resolve, reject) => {
                this.call(method, url, data, (err, res) => {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(res);
                    }
                });
            });
        }

        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('api-token', API.token());

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if ([200, 201, 204].includes(xhr.status)) {
                    callback(null, API.tryParseJSON(xhr.responseText));
                } else {
                    callback(xhr.responseText);
                }
            }
        };

        xhr.send(JSON.stringify(data));
    }

    static tryParseJSON(jsonString) {
        try {
            return JSON.parse(jsonString);
        } catch (error) {
            return null;
        }
    }
}

class Client extends API {
    static isPolling = false;
    static lastMessageId = 0;

    static async getMessages() {
        try {
            const r = await this.call('GET', '/api/messages');
            const messages = r.new_messages || r;

            if (!Array.isArray(messages)) {
                return Object.values(messages);
            }

            return messages;
        } catch (error) {
            console.error('Error fetching messages:', error);
            return [];
        }
    }

    static async getMessagesByIds(ids) {
        try {
            const r = await Client.getMessages();
            return r.messages || [];
        } catch (error) {
            console.error('Error fetching messages by IDs:', error);
            return [];
        }
    }

    static async getLatestEvents() {
        try {
            const r = await this.call('POST', `/api/long-poll/messages`, {
                last_message_id: Client.lastMessageId
            });

            if (r) {
                const messages = Object.values(r.new_messages);
                console.log('New messages:', messages);

                Client.lastMessageId = messages[messages.length - 1]?.id || 0;
                return messages;
            } else {
                return [];
            }
        } catch (error) {
            console.error('Error fetching latest events:', error);
            return [];
        }
    }

    static async sendMessage(message, replyId) {
        try {
            return await this.call('POST', '/api/messages', {
                message,
                reply_to_message_id: replyId ? replyId : undefined
            });
        } catch (error) {
            console.error('Error sending message:', error);
            throw error;
        }
    }

    static async deleteMessage(messageId) {
        try {
            return await this.call('DELETE', `/api/messages`, {
                id: messageId
            });
        } catch (error) {
            console.error('Error deleting message:', error);
            throw error;
        }
    }

    static async editMessage(messageId, message) {
        try {
            return await this.call('PATCH', `/api/messages`, {
                id: messageId,
                message
            });
        } catch (error) {
            console.error('Error editing message:', error);
            throw error;
        }
    }

    static async getProfile() {
        try {
            return await this.call('GET', '/get-profile');
        } catch (error) {
            console.error('Error fetching profile:', error);
            throw error;
        }
    }
}