import React from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import '../../index.css';
import '../../components/ui/Button/Button.css';
import './WelcomePage.css';
import Logo from '../../components/Logo/Logo';

const WelcomePage = () => {
  const navigate = useNavigate();

  return (
    <>
        <Logo/>
        <div className='button-container-welcome'>
            <Button text="Log In" onClick={() => navigate('/login')} />
            <Button text="Sign Up" onClick={() => navigate('/registration')} />
        </div>
    </>
  );
};

export default WelcomePage;