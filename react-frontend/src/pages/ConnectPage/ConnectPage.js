import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './ConnectPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';
import api from '../../services/api/api'; // Импорт основного API-клиента
import { GameContext } from '../../components/contexts/GameContext';

const ConnectPage = () => {
  const navigate = useNavigate();

  const [formValues, setFormValues] = useState({
    gameCode: ''
  });

  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const { setGameId } = useContext(GameContext); // Используем функцию для обновления gameId

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  const handleConnect = async () => {
    if (!formValues.gameCode) {
      setErrorMessage('Please enter a valid game code.');
      return;
    }

    try {
      setIsLoading(true);
      setErrorMessage('');
      const response = await api.post('/game/player/add', { uuid: formValues.gameCode });

      if (response.status === 'success') {
        alert('Connected to the game successfully!');
        setGameId(formValues.gameCode)
        navigate('/lobby');
      } else {
        setErrorMessage(response.message || 'Failed to connect to the game.');
      }
    } catch (error) {
      console.error('Error connecting to the game:', error);
      setErrorMessage('An error occurred while connecting to the game.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className='connect-game-container'>
      <h2>Connect Game</h2>
      <div className='connect-game-inputs'>
        <TextInput
          name="gameCode"
          type='text'
          placeholder="Game ID"
          value={formValues.gameCode}
          onChange={handleInputChange}
        />
      </div>
      {errorMessage && <p className="error-message">{errorMessage}</p>}
      <div className='connect-game-button'>
        <Button
          text={isLoading ? 'Connecting...' : 'Connect'}
          type="submit"
          onClick={handleConnect}
          disabled={isLoading}
        />
      </div>
    </div>
  );
};

export default ConnectPage;
