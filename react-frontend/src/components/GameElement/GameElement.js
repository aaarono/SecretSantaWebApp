import React from 'react';
import '../../index.css';
import './GameElement.css';
import showMore from '../../assets/showMore.svg';
import closeCircle from '../../assets/closeCircle.svg';
import { SlClose } from "react-icons/sl";
import { SlArrowRightCircle } from "react-icons/sl";


const GameElement = ({uuid, gameName, gameStatus, playersCount, playersMax, gameEnds }) => {
  return (
    <div className='game-element-container'>
        <h3 className='game-name'>{gameName}</h3>
        <p className='game-status'>Status: {gameStatus}</p>
        <p className='players-count'>Players: {playersCount}/{playersMax}</p>
        <p className='game-ends'>Ends in: {gameEnds}</p>
        <div className='game-links'>
            <SlArrowRightCircle />
            <SlClose />
        </div>
    </div>
  );
};

export default GameElement;