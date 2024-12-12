import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import '../../index.css';
import './ConnectPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';

const ConnectPage = () => {
  const navigate = useNavigate();

  const [formValues, setFormValues] = useState({
    gameCode: ''
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  return (
    <>
        <div className='connect-game-container'>
            <h2>Connect Game</h2>
            <div className='connect-game-inputs'>
                <TextInput
                name="gameCode"
                type='text'
                placeholder="Game ID"
                value={formValues.gameCode}
                onChange={handleInputChange}
                disabled
              />
            </div>
            <div className='connect-game-button'>
                <Button text="Connect" type="submit" onClick={() => navigate('/lobby')}/>
            </div>
        </div>
    </>
  );
};

export default ConnectPage;