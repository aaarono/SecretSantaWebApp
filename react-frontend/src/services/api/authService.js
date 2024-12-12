import api from './api';

/**
 * Аутентификация пользователя.
 * @param {string} loginOrEmail - Логин или email пользователя.
 * @param {string} password - Пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const login = async (loginOrEmail, password) => {
    try {
        return await api.post('/auth/login', { username: loginOrEmail, password });
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
};

/**
 * Регистрация нового пользователя.
 * @param {Object} userData - Данные пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const register = async (userData) => {
    try {
        return await api.post('/auth/register', userData);
    } catch (error) {
        console.error('Registration error:', error);
        throw error;
    }
};

/**
 * Выход пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const logout = async () => {
    try {
        return await api.post('/auth/logout');
    } catch (error) {
        console.error('Logout error:', error);
        throw error;
    }
};

/**
 * Смена пароля пользователя.
 * @param {string} currentPassword - Текущий пароль.
 * @param {string} newPassword - Новый пароль.
 * @returns {Promise<Object>} Ответ от сервера.
 */
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