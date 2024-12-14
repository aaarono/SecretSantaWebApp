import React, { useState, useContext, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './ConnectPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';
import api from '../../services/api/api';
import { GameContext } from '../../components/contexts/GameContext';
import useWebSocket from '../../hooks/useWebSocket';
import { UserContext } from '../../components/contexts/UserContext';

const ConnectPage = () => {
  const navigate = useNavigate();
  const [formValues, setFormValues] = useState({ gameCode: '' });
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const { setGameId } = useContext(GameContext);
  const { user } = useContext(UserContext);
  const [players, setPlayers] = useState([]);

  // Обробник отриманих повідомлень через WebSocket
  const handleWebSocketMessage = (message) => {
    switch (message.type) {
      case 'player_joined':
        setPlayers((prev) => [...prev, message.login]);
        break;
      case 'player_left':
        setPlayers((prev) => prev.filter((p) => p !== message.login));
        break;
      default:
        console.log('Отримано повідомлення:', message);
        break;
    }
  };

  // Використання хука useWebSocket
  const sendMessage = useWebSocket(handleWebSocketMessage);

  useEffect(() => {
    // Автоматично приєднуємося до гри після успішного підключення
    if (formValues.gameCode && user?.username) {
      sendMessage({ type: 'join_game', uuid: formValues.gameCode });
    }
  }, [formValues.gameCode, user, sendMessage]);

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
        setGameId(formValues.gameCode);
        navigate('/lobby/' + formValues.gameCode);
        // Надсилаємо повідомлення на приєднання до гри через WebSocket
        sendMessage({ type: 'join_game', uuid: formValues.gameCode });
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
      <ul>
        {players.map((player, index) => (
          <li key={index}>{player} приєднався до гри</li>
        ))}
      </ul>
    </div>
  );
};

export default ConnectPage;