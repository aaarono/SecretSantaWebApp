import React from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../components/ui/Button';
import '../index.css';

const MainPage = () => {
  const navigate = useNavigate();

  const styles = {
    centerContainer: {
      textAlign: 'center',
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
        <h1>Secret Santa</h1>
        <div style={styles.buttonContainer}>
            <Button text="Log In" onClick={() => navigate('/login')} />
            <Button text="Sign Up" onClick={() => navigate('/registration')} />
        </div>
    </div>
  );
};

export default MainPage;