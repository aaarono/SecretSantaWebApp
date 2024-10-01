// src/services/authService.js

const API_URL = 'http://localhost:8080/api/auth';

/**
 * Отправляет данные для аутентификации на бекенд.
 * @param {string} loginOrEmail - Логин или Email пользователя.
 * @param {string} password - Пароль пользователя.
 * @returns {Promise<Object>} Ответ от сервера.
 */
export const login = async (loginOrEmail, password) => {
  try {
    const response = await fetch(`${API_URL}/login`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
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
