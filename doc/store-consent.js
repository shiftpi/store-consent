window.storeConsent = (function() {
    const API_URL = window.storeConsentUrl || '';
    const COOKIE_NAME = 'storeConsent';
    const COOKIE_EXPIRES = 365;
    
    const getCookie = name => {
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
    
    const saveCookie = (name, value, expiresDays) => {
        const expires = new Date();
        expires.setTime(expires.getTime() + (expiresDays * 24 * 60 * 60 * 1000));
        
        document.cookie = `${name}=${value};expires=${expires.toUTCString()}`;
    };
    
    const update = (settings, id) => {
        doRequest('PUT', `${API_URL}/consent/${id}`, settings);
    };
    
    const create = settings => {
        doRequest('POST', `${API_URL}/consent`, settings).then(id => {
            saveCookie(COOKIE_NAME, id, COOKIE_EXPIRES);
        });
    };
    
    const doRequest = async (method, url, settings) => {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: Object.keys(settings)
              .map(p => `${encodeURIComponent(p)}=${encodeURIComponent(settings[p])}`)
              .join('&'),
        });
        
        if (!response.ok) {
            throw new Error(response.statusText);
        }
        
        return (await response.json()).id;
    };
    
    const store = settings => {
        if (getCookie(COOKIE_NAME)) {
            update(settings, getCookie(COOKIE_NAME));
        } else {
            create(settings);
        }
    };
    
    return {
        store,
    };
})();
