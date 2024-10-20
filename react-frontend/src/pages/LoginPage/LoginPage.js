import React from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './LoginPage.css';

const LoginForm = () => {
  const navigate = useNavigate();

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  return (
    <>
      <Logo/>
      <div className="section-container">
        <form className='login-form'>
            <TextInput name="username" placeholder={"Username"} errorCheck={validateUsername}/>
            <TextInput name="password" placeholder={"Password"} type='password' errorCheck={validatePassword}/>
            <div className='button-container-login'><Button text="Log In" onClick={() => navigate('/main')} /></div>
        </form>
      </div>
    </>
  );
};

export default LoginForm;