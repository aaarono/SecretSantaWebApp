// src/services/authService.js
import api from './api';

/**
 * Отправляет данные для аутентификации на бекенд.
 * @param {string} loginOrEmail - Логин или Email пользователя.
 * @param {string} password - Пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const login = async (loginOrEmail, password) => {
  try {
    return await api.post('/auth/login', {
      login_or_email: loginOrEmail,
      password: password,
    }, { credentials: 'include' });
  } catch (error) {
    throw error;
  }
};

/**
 * Регистрирует нового пользователя.
 * @param {string} user_name - Логин пользователя.
 * @param {string} email - Email пользователя.
 * @param {string} password - Пароль пользователя.
 * @param {string} first_name - Имя пользователя.
 * @param {string} last_name - Фамилия пользователя.
 * @param {string} phone - Телефон пользователя.
 * @param {string} gender - Пол пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const register = async (user_name, email, password, first_name, last_name, phone, gender) => {
  try {
    return await api.post('/auth/register', {
      user_name,
      email,
      password,
      first_name,
      last_name,
      phone,
      gender
    }, { credentials: 'include' });
  } catch (error) {
    throw error;
  }
};

/**
 * Выход пользователя из системы.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const logout = async () => {
  try {
    return await api.post('/auth/logout', null, { credentials: 'include' });
  } catch (error) {
    throw error;
  }
};

/**
 * Изменение пароля пользователя.
 * @param {string} currentPassword - Текущий пароль пользователя.
 * @param {string} newPassword - Новый пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const changePassword = async (currentPassword, newPassword) => {
  try {
    return await api.post('/auth/change-password', {
      current_password: currentPassword,
      new_password: newPassword,
    }, { credentials: 'include' });
  } catch (error) {
    throw error;
  }
};
