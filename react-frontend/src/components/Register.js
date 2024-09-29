import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

function Register() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    login: '',
    email: '',
    password: '',
    confirmPassword: ''
  });

  const [errors, setErrors] = useState({
    login: '',
    email: '',
    password: '',
    confirmPassword: ''
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
      case 'email':
        if (value.trim() === '') {
          error = 'Почта обязательна';
        } else {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(value)) {
            error = 'Некорректный формат почты';
          }
        }
        break;
      case 'password':
        if (value === '') {
          error = 'Пароль обязателен';
        } else if (value.length < 6) {
          error = 'Пароль должен содержать минимум 6 символов';
        }
        break;
      case 'confirmPassword':
        if (value === '') {
          error = 'Подтверждение пароля обязательно';
        } else if (value !== form.password) {
          error = 'Пароли не совпадают';
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

  const validateForm = () => {
    let valid = true;
    let newErrors = {
      login: '',
      email: '',
      password: '',
      confirmPassword: ''
    };

    // Проверка логина
    if (form.login.trim() === '') {
      newErrors.login = 'Логин обязателен';
      valid = false;
    }

    // Проверка почты
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (form.email.trim() === '') {
      newErrors.email = 'Почта обязательна';
      valid = false;
    } else if (!emailRegex.test(form.email)) {
      newErrors.email = 'Некорректный формат почты';
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

    // Проверка подтверждения пароля
    if (form.confirmPassword === '') {
      newErrors.confirmPassword = 'Подтверждение пароля обязательно';
      valid = false;
    } else if (form.password !== form.confirmPassword) {
      newErrors.confirmPassword = 'Пароли не совпадают';
      valid = false;
    }

    setErrors(newErrors);
    return valid;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (validateForm()) {
      // Здесь можно добавить отправку данных на сервер
      console.log('Регистрация прошла успешно:', form);
      navigate('/');
    }
  };

  return (
    <div className="container">
      <h2 className="title">Регистрация</h2>
      <form onSubmit={handleSubmit} className="form" noValidate>
        <div className="form-group">
          <input
            type="text"
            name="login"
            placeholder="Логин"
            value={form.login}
            onChange={handleChange}
            required
            className={`input ${errors.login ? 'input-error' : ''}`}
          />
          {errors.login && <span className="error">{errors.login}</span>}
        </div>
        <div className="form-group">
          <input
            type="email"
            name="email"
            placeholder="Почта"
            value={form.email}
            onChange={handleChange}
            required
            className={`input ${errors.email ? 'input-error' : ''}`}
          />
          {errors.email && <span className="error">{errors.email}</span>}
        </div>
        <div className="form-group">
          <input
            type="password"
            name="password"
            placeholder="Пароль"
            value={form.password}
            onChange={handleChange}
            required
            className={`input ${errors.password ? 'input-error' : ''}`}
          />
          {errors.password && <span className="error">{errors.password}</span>}
        </div>
        <div className="form-group">
          <input
            type="password"
            name="confirmPassword"
            placeholder="Подтверждение пароля"
            value={form.confirmPassword}
            onChange={handleChange}
            required
            className={`input ${errors.confirmPassword ? 'input-error' : ''}`}
          />
          {errors.confirmPassword && <span className="error">{errors.confirmPassword}</span>}
        </div>
        <button type="submit" className="button">
          Зарегистрироваться
        </button>
      </form>
    </div>
  );
}

export default Register;
