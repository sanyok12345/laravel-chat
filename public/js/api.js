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
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('api-token', API.token());

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
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