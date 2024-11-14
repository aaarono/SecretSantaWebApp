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

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    setShowErrors(true);

    if (validateUsername(formValues.username) && validatePassword(formValues.password)) {
      try {
        await login(formValues.username, formValues.password);
        navigate('/main');
      } catch (error) {
        setErrorMessage(error.message || 'Login failed. Please try again.');
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
          />
          <TextInput
            name="password"
            placeholder="Password"
            type="password"
            errorCheck={showErrors ? validatePassword : () => true}
            errorText="Password must be at least 6 characters long"
            value={formValues.password}
            onChange={handleInputChange}
          />
          {errorMessage && <div className="error-message">{errorMessage}</div>}
          <div className='button-container-login'>
            <Button text="Log In" type="submit" onClick={() => navigate('/main')}/>
          </div>
        </form>
      </div>
    </>
  );
};

export default LoginForm;
