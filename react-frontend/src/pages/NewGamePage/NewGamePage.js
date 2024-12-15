import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { GameContext } from '../../components/contexts/GameContext';
import api from '../../services/api/api';
import useWebSocket from '../../hooks/useWebSocket';
import '../../index.css';
import './NewGamePage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';

const NewGamePage = () => {
  const navigate = useNavigate();
  const { setGameId } = useContext(GameContext);
  const [formValues, setFormValues] = useState({
    name: '',
    budget: '',
    endsat: '',
    description: '',
  });
  const [isLoading, setIsLoading] = useState(false);

  const sendMessage = useWebSocket(
    (message) => {
      console.log('WebSocket message:', message);
    },
    (socket) => {
      console.log('WebSocket connected for game creation');
    }
  );

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  const handleSubmit = async () => {
    if (!formValues.name || !formValues.budget || !formValues.endsat) {
      alert('All fields are required!');
      return;
    }

    setIsLoading(true);
    try {
      const data = {
        name: formValues.name,
        budget: formValues.budget,
        endsAt: formValues.endsat,
        description: formValues.description,
      };

      const response = await api.post('/game/create', data);

      if (response.status === 'success') {
        setGameId(response.uuid);
        alert('Game created successfully!');
        navigate('/lobby/' + response.uuid);
        const resp = fetchGameCreator(response.uuid);
        // Use the existing sendMessage to send a WebSocket message
        sendMessage({ type: 'join_game', uuid: response.uuid, creator: resp.creator});
      } else {
        alert(response.message || 'Failed to create game');
      }
    } catch (error) {
      console.error('Failed to create game:', error);
      alert('Error creating game.');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchGameCreator = async (uuid) => {
    const response = await api.get(`/game/player/creator?uuid=${uuid}`);
    console.log('Response from server:', response.creator);
    if (response.status === 'success') {
      return response.creator;
    }
    return null;
  }; 


  return (
    <div className="new-game-container">
      <h2>New Game</h2>
      <div className="new-game-inputs">
        <TextInput
          name="name"
          type="text"
          placeholder="Game Name"
          value={formValues.name}
          onChange={handleInputChange}
        />
        <TextInput
          name="endsat"
          type="date"
          placeholder="Deadline"
          value={formValues.endsat}
          onChange={handleInputChange}
        />
        <TextInput
          name="description"
          type="text"
          placeholder="Description"
          value={formValues.description}
          onChange={handleInputChange}
        />
        <TextInput
          name="budget"
          type="number"
          placeholder="Budget"
          value={formValues.budget}
          onChange={handleInputChange}
        />
      </div>
      <div className="new-game-button">
        <Button
          text={isLoading ? 'Creating...' : 'Create'}
          type="submit"
          onClick={handleSubmit}
          disabled={isLoading}
        />
      </div>
    </div>
  );
};

export default NewGamePage;
