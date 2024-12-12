import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './AdminPage.css';

const AdminPage = () => {
  const navigate = useNavigate();
  const [formValues, setFormValues] = useState({
    username: '',
    password: '',
  });
  const [showErrors, setShowErrors] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);


  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues((prev) => ({ ...prev, [name]: value }));
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    setShowErrors(true);
    setErrorMessage('');
  };

  return (
    <>
      <Logo />
      <div className="section-container">
        <form className='login-form' onSubmit={handleFormSubmit}>
          <TextInput
            name="username"
            placeholder="Username"
            onChange={handleInputChange}
            showErrors={showErrors}
          />
          <TextInput
            name="password"
            placeholder="Password"
            type="password"
            onChange={handleInputChange}
            showErrors={showErrors}
          />
          <div className='button-container-login'>
            <Button text={isLoading ? "Logging In..." : "Log In"} type="submit" disabled={isLoading} />
          </div>
        </form>
      </div>
    </>
  );
};

export default AdminPage;
