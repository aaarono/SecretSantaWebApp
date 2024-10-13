import React from 'react';
import Button from '../components/ui/Button';
import TextInput from '../components/ui/TextInput';
import '../index.css';

const LoginForm = () => {
  const styles = {
    centerContainer: {
      textAlign: 'center',
      display: 'flex',
      gap: '10px',
    },
  };

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  return (
    <form className='section-container'>
      <div style={styles.centerContainer}>
        <h1>Login</h1>
        <TextInput name="username" placeholder={"Username"} errorCheck={validateUsername} errorText="Username must be at least 3 characters long" />
        <TextInput name="password" placeholder={"Password"} type='password' errorCheck={validatePassword} errorText="Password must be at least 6 characters long" />
        <Button text="Log In" onClick={() => console.log('Log In clicked')} />
      </div>
    </form>
  );
};

export default LoginForm;