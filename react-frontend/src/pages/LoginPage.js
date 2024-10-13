import React from 'react';
import Button from '../components/ui/Button';
import TextInput from '../components/ui/TextInput';
import '../index.css';

const LoginForm = () => {
  const styles = {
    centerContainer: {
      textAlign: 'center',
    },
    buttonMain: {
      margin: 'auto',
      marginTop: '20px',
    }
  };

  const validateUsername = (username) => {
    return username.length >= 3;
  };

  const validatePassword = (password) => {
    return password.length >= 6;
  };

  return (
    <div>
      <h1>Secret Santa</h1>
      <div className="section-container">
        <form>
          <div style={styles.centerContainer}>
            <TextInput name="username" placeholder={"Username"} errorCheck={validateUsername} errorText="Username must be at least 3 characters long" />
            <TextInput name="password" placeholder={"Password"} type='password' errorCheck={validatePassword} errorText="Password must be at least 6 characters long" />
            <Button style={styles.buttonMain} text="Log In" onClick={() => console.log('Log In clicked')} />
          </div>
        </form>
      </div>
    </div>
  );
};

export default LoginForm;