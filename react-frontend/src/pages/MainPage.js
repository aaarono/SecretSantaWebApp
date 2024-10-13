import React from 'react';
import Button from './Button';
import '../index.css';

const SecretSantaPage = () => {
  const styles = {
    backgroundContainer: {
      backgroundColor: 'var(--main-bg-dark)',
      height: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
    },
    centerContainer: {
      textAlign: 'center',
    },
    title: {
      color: 'var(--text-dark)',
      fontFamily: 'cursive',
      fontSize: '3rem',
    },
    buttonContainer: {
      marginTop: '20px',
      display: 'flex',
      gap: '10px',
      justifyContent: 'center',
    },
  };

  return (
    <div style={styles.backgroundContainer}>
      <div style={styles.centerContainer}>
        <h1 style={styles.title}>Secret Santa</h1>
        <div style={styles.buttonContainer}>
          <Button text="Log In" onClick={() => console.log('Log In clicked')} className="button-main" />
          <Button text="Sign Up" onClick={() => console.log('Sign Up clicked')} className="button-main" />
        </div>
      </div>
    </div>
  );
};

export default SecretSantaPage;