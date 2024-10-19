class API {
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
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    callback(null, JSON.parse(xhr.responseText));
                } else {
                    callback(xhr.responseText);
                }
            }
        };

        xhr.send(JSON.stringify(data));   
    }
}