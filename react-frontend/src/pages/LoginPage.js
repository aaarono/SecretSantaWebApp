import React from 'react';
import Button from '../components/ui/Button';
import TextInput from '../components/ui/TextInput';
import '../index.css';

const  LoginPage = () => {
  const styles = {
    centerContainer: {
      textAlign: 'center',
    },
  };

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  return (
    <form style={styles.backgroundContainer}>
      <div style={styles.centerContainer}>
        <h1>Login</h1>
        <TextInput placeholder={"Username"} errorCheck={validateUsername} errorText="Username must be at least 3 characters long" />
        <TextInput placeholder={"Password"} type='password' errorCheck={validatePassword} errorText="Password must be at least 6 characters long" />
        <Button text="Log In" onClick={() => console.log('Log In clicked')} />
      </div>
    </form>
  );
};

export default LoginPage;