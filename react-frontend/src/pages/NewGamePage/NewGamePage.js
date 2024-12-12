import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import '../../index.css';
import './NewGamePage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';

const NewGamePage = () => {
  const navigate = useNavigate();

  const [formValues, setFormValues] = useState({
    name: '',
    budget: '',
    endsat: ''
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  return (
    <>
        <div className='new-game-container'>
            <h2>New Game</h2>
            <div className='new-game-inputs'>

                <TextInput
                name="name"
                type='text'
                placeholder="Game Name"
                value={formValues.name}
                onChange={handleInputChange}
                disabled
              />

              {/* <TextInput
                name="maxPlayersCount"
                type='number'
                placeholder="Players"
                value={formValues.maxPlayersCount}
                onChange={handleInputChange}
                disabled
              /> */}

              <TextInput
                  name="endsat"
                  type='date'
                  placeholder="Deadline"
                  value={formValues.endsat}
                  onChange={handleInputChange}
                  disabled
              />

              <TextInput
                name="budget"
                type='number'
                placeholder="Budget"
                value={formValues.budget}
                onChange={handleInputChange}
                disabled
              />
            </div>
            <div className='new-game-button'>
              <Button text="Create" type="submit" onClick={() => navigate('/lobby')} />
            </div>
        </div>
    </>
  );
};

export default NewGamePage;