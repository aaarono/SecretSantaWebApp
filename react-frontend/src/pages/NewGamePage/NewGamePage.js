import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api/api'; // Подключаем API
import '../../index.css';
import './NewGamePage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';

const NewGamePage = () => {
  const navigate = useNavigate();

  const [formValues, setFormValues] = useState({
    name: '',
    budget: '',
    endsat: '',
    description: '',
  });

  const [isLoading, setIsLoading] = useState(false);

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
        description: formValues.description, // Если нужно описание, добавьте его в форму
      };

      const response = await api.post('/game/create', data);

      if (response.status === 'success') {
        alert('Game created successfully!');
        navigate('/lobby'); // Перенаправляем пользователя на страницу лобби
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
