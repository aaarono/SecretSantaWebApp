import api from './api';

export const login = async (loginOrEmail, password) => {
    try {
        const response = await api.post('/auth/login', { username: loginOrEmail, password });
        // Store CSRF token from response
        if (response.csrf_token) {
            localStorage.setItem('csrf_token', response.csrf_token);
        }
        return response;
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
};

export const register = async (userData) => {
    try {
        return await api.post('/auth/register', userData);
    } catch (error) {
        console.error('Registration error:', error);
        throw error;
    }
};

export const logout = async () => {
    try {
        const response = await api.post('/auth/logout');
        localStorage.removeItem('PHPSESSID');
        localStorage.removeItem('csrf_token');
        return response;
    } catch (error) {
        console.error('Logout error:', error);
        throw error;
    }
};

export const changePassword = async (currentPassword, newPassword) => {
    try {
        return await api.post('/auth/change-password', { current_password: currentPassword, new_password: newPassword });
    } catch (error) {
        console.error('Password change error:', error);
        throw error;
    }
};

export const checkAuth = async () => {
  try {
    return await api.get('/auth/check');
  } catch (error) {
    console.error('Auth check error:', error);
    throw error;
  }
};