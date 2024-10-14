import React from 'react';
import Button from '../components/ui/Button';
import TextInput from '../components/ui/TextInput';
import '../index.css';

const LoginForm = () => {
  const styles = {
    buttonMain: {
      margin: '10px auto',
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
            <TextInput name="username" placeholder={"Username"} errorCheck={validateUsername}/>
            <TextInput name="password" placeholder={"Password"} type='password' errorCheck={validatePassword}/>
            <Button style={styles.buttonMain} text="Log In" onClick={() => console.log('Log In clicked')} />
        </form>
      </div>
    </div>
  );
};

export default LoginForm;