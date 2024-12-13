import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './ConnectPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';
import api from '../../services/api/api';
import { GameContext } from '../../components/contexts/GameContext';
import useWebSocket from '../../hooks/useWebSocket';

const ConnectPage = () => {
  const navigate = useNavigate();
  const [formValues, setFormValues] = useState({ gameCode: '' });
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const { setGameId } = useContext(GameContext);
  const [players, setPlayers] = useState([]);

  // const handleWebSocketMessage = (message) => {
  //   if (message.type === 'player_joined') {
  //     setPlayers((prev) => [...prev, message.player]);
  //   } else if (message.type === 'player_left') {
  //     setPlayers((prev) => prev.filter((p) => p !== message.player));
  //   }
  // };

  // const sendMessage = useWebSocket(
  //   'ws://localhost:9090', // Убедитесь, что адрес корректен
  //   handleWebSocketMessage,
  //   (socket) => {
  //     console.log('WebSocket connected');
  //     if (formValues.gameCode) {
  //       socket.send(JSON.stringify({ type: 'join_game', uuid: formValues.gameCode }));
  //     }
  //   }
  // );
  

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
        navigate('/lobby');
        // sendMessage({ type: 'join_game', uuid: formValues.gameCode });
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
          <li key={index}>{player}</li>
        ))}
      </ul>
    </div>
  );
};

export default ConnectPage;
