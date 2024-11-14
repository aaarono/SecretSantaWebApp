import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import SelectInput from '../../components/ui/SelectInput/SelectInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './RegistrationPage.css';
import { register } from '../../services/api/authService';

const RegistrationForm = () => {
  const navigate = useNavigate();
  const [showErrors, setShowErrors] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [formValues, setFormValues] = useState({
    email: '',
    username: '',
    phone: '',
    name: '',
    surname: '',
    password: '',
    passwordRepeat: '',
  });

  const validateEmail = (email) => {
    return email.includes('@');
  };

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePhone = (phone) => {
    return phone.length >= 10;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  const validateSecondPassword = (passwordRepeat) => {
    return passwordRepeat === formValues.password;
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    setShowErrors(true);
    if (
      validateEmail(formValues.email) &&
      validateUsername(formValues.username) &&
      validatePhone(formValues.phone) &&
      validatePassword(formValues.password) &&
      validateSecondPassword(formValues.passwordRepeat)
    ) {
      try {
        await register(
          formValues.username,
          formValues.email,
          formValues.password,
          formValues.name,
          formValues.surname,
          formValues.phone,
          "MALE" // Replace this with the actual value from SelectInput
        );
        navigate('/main');
      } catch (error) {
        setErrorMessage(error.message || 'Registration failed. Please try again.');
      }
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  return (
    <>
      <Logo />
      <div className='section-container'>
        <form className='reg-form' onSubmit={handleFormSubmit}>
          <TextInput
            name="email"
            type='email'
            placeholder="E-mail"
            errorCheck={showErrors ? validateEmail : () => true}
            errorText="Email must be valid"
            value={formValues.email}
            onChange={handleInputChange}
          />
          <TextInput
            name="username"
            placeholder="Username"
            errorCheck={showErrors ? validateUsername : () => true}
            errorText="Username must be at least 3 characters long"
            value={formValues.username}
            onChange={handleInputChange}
          />
          <TextInput
            name="phone"
            type='tel'
            placeholder="Phone"
            errorCheck={showErrors ? validatePhone : () => true}
            errorText="Phone must be valid"
            value={formValues.phone}
            onChange={handleInputChange}
          />
          <TextInput
            name="name"
            placeholder="Name"
            errorCheck={showErrors ? validateUsername : () => true}
            errorText="Name must be at least 3 characters long"
            value={formValues.name}
            onChange={handleInputChange}
          />
          <TextInput
            name="surname"
            placeholder="Surname"
            errorCheck={showErrors ? validateUsername : () => true}
            errorText="Surname must be at least 3 characters long"
            value={formValues.surname}
            onChange={handleInputChange}
          />
          <SelectInput />
          <TextInput
            name="password"
            placeholder="Password"
            type='password'
            errorCheck={showErrors ? validatePassword : () => true}
            errorText="Password must be at least 6 characters long"
            value={formValues.password}
            onChange={handleInputChange}
          />
          <TextInput
            name="passwordRepeat"
            placeholder="Repeat password"
            type='password'
            errorCheck={showErrors ? validateSecondPassword : () => true}
            errorText="Passwords must match"
            value={formValues.passwordRepeat}
            onChange={handleInputChange}
          />
          {errorMessage && <div className="error-message">{errorMessage}</div>}
          <div className='button-container-signup'>
            <Button text="Sign Up" type="submit" onClick={() => navigate('/main')}/>
          </div>
        </form>
      </div>
    </>
  );
};

export default RegistrationForm;
