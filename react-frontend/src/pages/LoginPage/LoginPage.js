import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './LoginPage.css';
import { login } from '../../services/api/authService';

const LoginForm = () => {
  const navigate = useNavigate();
  const [formValues, setFormValues] = useState({
    username: '',
    password: '',
  });
  const [showErrors, setShowErrors] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false); // Состояние загрузки

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
        console.log(response)
        if (response.status === 'success') {
          navigate('/');
        } else {
          setErrorMessage(response.message || 'Login failed. Please try again.');
        }
      } catch (error) {
        // Обработка ошибок, полученных от сервера
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
        <form className='login-form' onSubmit={handleFormSubmit}>
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
          <div className='button-container-login'>
            <Button text={isLoading ? "Logging In..." : "Log In"} type="submit" disabled={isLoading} />
          </div>
        </form>
      </div>
    </>
  );
};

export default LoginForm;
