window.storeConsent = (function() {
    const API_URL = window.storeConsentUrl || '';
    const COOKIE_NAME = 'storeConsent';
    const COOKIE_EXPIRES = 365;

    const getCookie = function(name) {
        const cookies = decodeURIComponent(document.cookie).split(';');
        let match = null;

        cookies.forEach(function(cookie) {
            const parts = cookie.split('=').map(function(p) {
                return p.trim();
            });

            if (parts[0] === name) {
                match = parts[1];
            }
        });

        return match;
    };

    const saveCookie = function(name, value, expiresDays) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (expiresDays * 24 * 60 * 60 * 1000));

        document.cookie = `${name}=${value};expires=${expires.toUTCString()}`;
    };

    const update = function(settings, id) {
        doRequest('PUT', `${API_URL}/consent/${id}`, settings);
    };

    const create = function(settings, cb) {
        const newCb = function(response) {
            cb(response.id || null);
        };

        doRequest('POST', `${API_URL}/consent`, settings, newCb);
    };

    const doRequest = function(method, url, settings, cb) {
        const request = new XMLHttpRequest();
        let response = '';

        request.onreadystatechange = function() {
            if (request.readyState === XMLHttpRequest.DONE && request.status === 200 && cb) {
                cb(JSON.parse(request.responseText));
            }
        };

        request.open(method, url);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        const parts = [];
        for (let p in settings) {
            parts.push(p + '=' + settings[p]);
        }

        request.send(parts.join('&'));

        return response;
    };

    const store = function(settings) {
        if (getCookie(COOKIE_NAME)) {
            update(settings, getCookie(COOKIE_NAME));
        } else {
            create(settings, function(id) {
                if (id) {
                    saveCookie(COOKIE_NAME, id, COOKIE_EXPIRES);
                }
            });
        }
    };

    return {
        store: store
    };
})();