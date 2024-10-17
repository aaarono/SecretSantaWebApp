import React, { useState } from 'react';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import SelectInput from '../../components/ui/SelectInput/SelectInput';
import Logo from '../../components/Logo';
import '../../index.css';
import './RegistrationPage.css';

const RegistrationForm = () => {
  const [showErrors, setShowErrors] = useState(false);
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

  const handleFormSubmit = (e) => {
    e.preventDefault();
    setShowErrors(true);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  return (
    <>
    <Logo/>
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
          <SelectInput/>
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
          <div className='button-container-signup'><Button text="Sign Up" type="submit"/></div>
      </form>
      </div>
    </>
  );
};

export default RegistrationForm;