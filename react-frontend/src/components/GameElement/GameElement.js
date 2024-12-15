import React from 'react';
import '../../index.css';
import './GameElement.css';
import { SlArrowRightCircle } from "react-icons/sl";
import { SlClose } from "react-icons/sl";
import { useNavigate } from 'react-router-dom';

const GameElement = ({ uuid, gameName, gameStatus, playersCount, playersMax, gameEnds }) => {
  const navigate = useNavigate();

  const goToLobby = () => {
    navigate(`/lobby/${uuid}`);
  };

  return (
    <div className='game-element-container'>
      <h3 className='game-name'>{gameName}</h3>
      <p className='game-status'>Status: {gameStatus}</p>
      <p className='players-count'>Players: {playersCount}/{playersMax}</p>
      <p className='game-ends'>Ends in: {gameEnds}</p>
      <div className='game-links'>
        <SlArrowRightCircle onClick={goToLobby} style={{ cursor: 'pointer' }} />
        <SlClose />
      </div>
    </div>
  );
};

export default GameElement;
