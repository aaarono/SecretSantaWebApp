import React from 'react';
import Button from '../components/ui/Button';
import '../index.css';

const SecretSantaPage = () => {
  const styles = {
    centerContainer: {
      textAlign: 'center',
      alignItems: 'center',
      justifyContent: 'center',
    },
    buttonContainer: {
      marginTop: '20px',
      display: 'flex',
      gap: '10px',
      justifyContent: 'center',
    },
  };

  return (
      <div style={styles.centerContainer}>
        <h1 style={styles.title}>Secret Santa</h1>
        <div style={styles.buttonContainer}>
          <Button text="Log In" onClick={() => console.log('Log In clicked')} className="button-main" />
          <Button text="Sign Up" onClick={() => console.log('Sign Up clicked')} className="button-main" />
        </div>
      </div>
  );
};

export default SecretSantaPage;