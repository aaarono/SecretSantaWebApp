import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './LoginPage.css';
import { login } from '../../services/api/authService';
import { UserContext } from '../../components/contexts/UserContext'; // Подключаем контекст пользователя

const LoginForm = () => {
  const navigate = useNavigate();
  const { fetchUserData } = useContext(UserContext); // Достаем функцию fetchUserData из контекста
  const [formValues, setFormValues] = useState({
    username: '',
    password: '',
  });
  const [showErrors, setShowErrors] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues((prev) => ({ ...prev, [name]: value }));
  };

  const isFormValid = () => {
    return validateUsername(formValues.username) && validatePassword(formValues.password);
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    setShowErrors(true);
    setErrorMessage('');

    if (isFormValid()) {
      setIsLoading(true);
      try {
        const response = await login(formValues.username, formValues.password);
        if (response.status === 'success') {
          await fetchUserData(); // Вызываем фетч данных пользователя
          navigate('/'); // Перенаправляем на главную страницу
        } else {
          setErrorMessage(response.message || 'Login failed. Please try again.');
        }
      } catch (error) {
        setErrorMessage(
          error.response?.message || 'Login failed. Please try again.'
        );
      } finally {
        setIsLoading(false);
      }
    }
  };

  return (
    <>
      <Logo />
      <div className="section-container">
        <form className="login-form" onSubmit={handleFormSubmit}>
          <TextInput
            name="username"
            placeholder="Username"
            errorCheck={showErrors ? validateUsername : () => true}
            errorText="Username must be at least 3 characters long"
            value={formValues.username}
            onChange={handleInputChange}
            showErrors={showErrors}
          />
          <TextInput
            name="password"
            placeholder="Password"
            type="password"
            errorCheck={showErrors ? validatePassword : () => true}
            errorText="Password must be at least 6 characters long"
            value={formValues.password}
            onChange={handleInputChange}
            showErrors={showErrors}
          />
          {errorMessage && <div className="error-message">{errorMessage}</div>}
          <div className="button-container-login">
            <Button text={isLoading ? "Logging In..." : "Log In"} type="submit" disabled={isLoading} />
          </div>
        </form>
      </div>
    </>
  );
};

export default LoginForm;
