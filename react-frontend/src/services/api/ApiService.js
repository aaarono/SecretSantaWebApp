class ApiService {
    constructor(baseURL = '') {
        this.baseURL = baseURL;
        this.defaultHeaders = {
            'Content-Type': 'application/json',
        };
    }

    getFullURL(endpoint) {
        return `${this.baseURL}${endpoint}`;
    }

    async request(method, endpoint, body = null, headers = {}) {
        const url = this.getFullURL(endpoint);
    
        const isFormData = body instanceof FormData;
    
        // Include CSRF token from localStorage
        const csrfToken = localStorage.getItem('csrf_token'); 
        if (csrfToken) {
            headers['X-CSRF-Token'] = csrfToken;
        }
    
        headers = {
            ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
            ...headers,
        };
    
        const options = {
            method: method.toUpperCase(),
            headers,
            credentials: 'include', // Include cookies for session management
        };
    
        if (body) {
            options.body = isFormData ? body : JSON.stringify(body);
        }
    
        try {
            const response = await fetch(url, options);
    
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error ${response.status}: ${errorText}`);
            }
    
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            return await response.text();
        } catch (error) {
            console.error(`Request ${method} ${endpoint} failed:`, error);
            throw error;
        }
    }

    get(endpoint, headers = {}) {
        return this.request('GET', endpoint, null, headers);
    }

    post(endpoint, body, headers = {}) {
        return this.request('POST', endpoint, body, headers);
    }

    put(endpoint, body, headers = {}) {
        return this.request('PUT', endpoint, body, headers);
    }

    delete(endpoint, body = null, headers = {}) {
        return this.request('DELETE', endpoint, body, headers);
    }
}

export default ApiService;
