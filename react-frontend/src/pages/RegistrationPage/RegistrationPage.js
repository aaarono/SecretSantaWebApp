import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './RegistrationPage.css';
import { register } from '../../services/api/authService';

const RegistrationForm = () => {
  const navigate = useNavigate();
  const [showErrors, setShowErrors] = useState(true);
  const [errorMessage, setErrorMessage] = useState('');
  const [formValues, setFormValues] = useState({
    email: '',
    username: '',
    phone: '',
    name: '',
    surname: '',
    gender: '',
    password: '',
    passwordRepeat: '',
  });

  const validateEmail = (email) => {
    return typeof email === 'string' && email.includes('@') && email.includes('.');
  };
  
  const validateUsername = (username) => {
    return typeof username === 'string' && username.trim().length >= 3;
  };
  
  const validatePhone = (phone) => {
    return typeof phone === 'string' && /^\d{10,}$/.test(phone.replace(/\D/g, ''));
  };
  
  const validatePassword = (password) => {
    return typeof password === 'string' && password.trim().length >= 6;
  };
  
  const validateSecondPassword = (passwordRepeat) => {
    // return passwordRepeat === formValues.password && validatePassword(formValues.password);
    return true;
  };
  
  const validateGender = (gender) => {
    return gender !== '';
  };
  
  const validateName = (name) => {
    return typeof name === 'string' && name.trim().length >= 2;
  };
  
  const validateSurname = (surname) => {
    return typeof surname === 'string' && surname.trim().length >= 2;
  };

  const isFormValid = () => {
    return (
      validateEmail(formValues.email)
      //validateUsername(formValues.username) &&
      //validatePhone(formValues.phone) &&
      //validateName(formValues.name) &&
      //validateSurname(formValues.surname) &&
      //validateGender(formValues.gender) &&
      //validatePassword(formValues.password) 
      //validateSecondPassword(formValues.passwordRepeat)
    );
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    setShowErrors(true);
    console.log(formValues)

    if (isFormValid()) {
      const userData = {
        username: formValues.username,
        email: formValues.email,
        password: formValues.password,
        name: formValues.name,
        surname: formValues.surname,
        phone: formValues.phone,
        gender: formValues.gender,
      };

      try {
        await register(userData);
        navigate('/login');
        console.log(userData);
      } catch (error) {
        console.log(error);
        console.error(error);
        setErrorMessage(error.message || 'Registration failed. Please try again.');
      }
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues((prev) => ({ ...prev, [name]: value }));
  };

  return (
    <>
      <Logo />
      <div className="section-container">
        <form className="reg-form" onSubmit={handleFormSubmit}>
          <TextInput
            name="email"
            type="email"
            placeholder="E-mail"
            errorCheck={showErrors ? validateEmail : () => true}
            errorText="Email must be valid (e.g. example@domain.com)"
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
            type="tel"
            placeholder="Phone"
            errorCheck={showErrors ? validatePhone : () => true}
            errorText="Phone must contain at least 10 digits"
            value={formValues.phone}
            onChange={handleInputChange}
          />
          <TextInput
            name="name"
            placeholder="Name"
            errorCheck={showErrors ? validateName : () => true}
            errorText="Name must be at least 2 characters long"
            value={formValues.name}
            onChange={handleInputChange}
          />
          <TextInput
            name="surname"
            placeholder="Surname"
            errorCheck={showErrors ? validateSurname : () => true}
            errorText="Surname must be at least 2 characters long"
            value={formValues.surname}
            onChange={handleInputChange}
          />
          <div className="select-input-container">
            <select
              name="gender"
              value={formValues.gender}
              onChange={handleInputChange}
            >
              <option value="" disabled>
                Gender
              </option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            {showErrors && !validateGender(formValues.gender) && (
              <div className="error-text">Please select a gender</div>
            )}
          </div>
          <TextInput
            name="password"
            placeholder="Password"
            type="password"
            errorCheck={showErrors ? validatePassword : () => true}
            errorText="Password must be at least 6 characters long"
            value={formValues.password}
            onChange={handleInputChange}
          />
          <TextInput
            name="passwordRepeat"
            placeholder="Repeat password"
            type="password"
            errorCheck={showErrors ? validateSecondPassword : () => true}
            errorText="Passwords must match"
            value={formValues.passwordRepeat}
            onChange={handleInputChange}
          />
          {errorMessage && <div className="error-message">{errorMessage}</div>}
          <div className="button-container-signup">
            <Button text="Sign Up" type="submit" />
          </div>
        </form>
      </div>
    </>
  );
};

export default RegistrationForm;
