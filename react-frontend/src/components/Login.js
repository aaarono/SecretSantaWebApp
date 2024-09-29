// src/components/Login.js
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

function Login() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    login: '',
    password: ''
  });

  const [errors, setErrors] = useState({
    login: '',
    password: ''
  });

  const [touched, setTouched] = useState({
    login: false,
    password: false
  });

  // Функция для валидации отдельного поля
  const validateField = (name, value) => {
    let error = '';

    switch (name) {
      case 'login':
        if (value.trim() === '') {
          error = 'Логин обязателен';
        }
        break;
      case 'password':
        if (value === '') {
          error = 'Пароль обязателен';
        } else if (value.length < 6) {
          error = 'Пароль должен содержать минимум 6 символов';
        }
        break;
      default:
        break;
    }

    setErrors((prevErrors) => ({
      ...prevErrors,
      [name]: error
    }));
  };

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prevForm) => ({
      ...prevForm,
      [name]: value
    }));

    // Валидируем поле при каждом изменении
    validateField(name, value);
  };

  const handleBlur = (e) => {
    const { name } = e.target;
    setTouched((prevTouched) => ({
      ...prevTouched,
      [name]: true
    }));
  };

  const validateForm = () => {
    let valid = true;
    let newErrors = {
      login: '',
      password: ''
    };

    // Проверка логина
    if (form.login.trim() === '') {
      newErrors.login = 'Логин обязателен';
      valid = false;
    }

    // Проверка пароля
    if (form.password === '') {
      newErrors.password = 'Пароль обязателен';
      valid = false;
    } else if (form.password.length < 6) {
      newErrors.password = 'Пароль должен содержать минимум 6 символов';
      valid = false;
    }

    setErrors(newErrors);
    return valid;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (validateForm()) {
      // Здесь можно добавить отправку данных на сервер для аутентификации
      console.log('Вход выполнен успешно:', form);
      navigate('/');
    }
  };

  return (
    <div className="container">
      <h2 className="title">Вход</h2>
      <form onSubmit={handleSubmit} className="form" noValidate>
        <div className="form-group">
          <input
            type="text"
            name="login"
            placeholder="Логин"
            value={form.login}
            onChange={handleChange}
            onBlur={handleBlur}
            required
            className={`input ${errors.login && touched.login ? 'input-error' : ''}`}
          />
          {errors.login && touched.login && <span className="error">{errors.login}</span>}
        </div>
        <div className="form-group">
          <input
            type="password"
            name="password"
            placeholder="Пароль"
            value={form.password}
            onChange={handleChange}
            onBlur={handleBlur}
            required
            className={`input ${errors.password && touched.password ? 'input-error' : ''}`}
          />
          {errors.password && touched.password && <span className="error">{errors.password}</span>}
        </div>
        <button type="submit" className="button">
          Войти
        </button>
      </form>
    </div>
  );
}

export default Login;
