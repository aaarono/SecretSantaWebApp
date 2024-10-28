// src/services/authService.js

const API_URL = 'http://localhost:8000/api/auth';

/**
 * Отправляет данные для аутентификации на бекенд.
 * @param {string} loginOrEmail - Логин или Email пользователя.
 * @param {string} password - Пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const login = async (loginOrEmail, password) => {
  try {
    const response = await fetch(`${API_URL}/login`, {
      method: 'POST', // Изменено с 'GET' на 'POST'
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include', // Для отправки куки с запросом
      body: JSON.stringify({
        login_or_email: loginOrEmail,
        password: password,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      // Генерируем ошибку с сообщением от сервера
      const error = new Error(data.message || 'Произошла ошибка при входе.');
      error.status = response.status;
      throw error;
    }

    return data;
  } catch (error) {
    // Пробрасываем ошибку для обработки в компоненте
    throw error;
  }
};

/**
 * Регистрирует нового пользователя.
 * @param {string} user_name - Логин пользователя.
 * @param {string} email - Email пользователя.
 * @param {string} password - Пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const register = async (user_name, email, password, first_name, last_name, phone, gender) => {
  try {
    const response = await fetch(`${API_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({
        user_name,
        email,
        password,
        first_name,
        last_name,
        phone,
        gender
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      const error = new Error(data.message || 'Произошла ошибка при регистрации.');
      error.status = response.status;
      throw error;
    }

    return data;
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
    const response = await fetch(`${API_URL}/logout`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    });

    const data = await response.json();

    if (!response.ok) {
      const error = new Error(data.message || 'Произошла ошибка при выходе.');
      error.status = response.status;
      throw error;
    }

    return data;
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
    const response = await fetch(`${API_URL}/change-password`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({
        current_password: currentPassword,
        new_password: newPassword,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      const error = new Error(data.message || 'Произошла ошибка при смене пароля.');
      error.status = response.status;
      throw error;
    }

    return data;
  } catch (error) {
    throw error;
  }
};
