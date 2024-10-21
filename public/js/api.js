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
                if ([
                    200, 201, 204
                ].includes(xhr.status)) {
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
        return this.call('GET', '/api/messages');
    }

    static async getLatestEvents() {
        const r = await this.call('POST', `/api/long-poll/messages`, {
            last_message_id: Client.lastMessageId
        });

        if (r && Array.isArray(r.new_messages) && r.new_messages.length > 0) {
            Client.lastMessageId = r.new_messages[r.new_messages.length - 1]?.id || 0;
            return r.new_messages;
        } else {
            Client.lastMessageId = 0;
            return [];
        }
    }

    static async sendMessage(message, replyId) {
        return await this.call('POST', '/api/messages', { 
            message,
            reply_to_message_id: replyId ? replyId : undefined
        });
    }

    static async deleteMessage(messageId) {
        return await this.call('DELETE', `/api/messages`, {
            id: messageId
        });
    }

    static async editMessage(messageId, message) {
        return await this.call('PATCH', `/api/messages`, {
            id: messageId,
            message
        });
    }

    static async getProfile() {
        return await this.call('GET', '/get-profile');
    }
}
