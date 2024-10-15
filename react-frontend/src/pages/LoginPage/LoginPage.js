import React from 'react';
import Button from '../../components/ui/Button/Button';
import TextInput from '../../components/ui/TextInput/TextInput';
import '../../index.css';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo';

const LoginForm = () => {
  const navigate = useNavigate();

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  return (
    <div>
      <Logo/>
      <div className="section-container">
        <form>
            <TextInput name="username" placeholder={"Username"} errorCheck={validateUsername}/>
            <TextInput name="password" placeholder={"Password"} type='password' errorCheck={validatePassword}/>
            <Button text="Log In" onClick={() => navigate('/main')} />
        </form>
      </div>
    </div>
  );
};

export default LoginForm;