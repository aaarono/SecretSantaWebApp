import React from 'react';
import '../../index.css';
import './GameElement.css';
import showMore from '../../assets/showMore.svg';
import closeCircle from '../../assets/closeCircle.svg';


const GameElement = ({ gameName, gameStatus, playersCount, playersMax, gameEnds }) => {
  return (
    <div className='game-element-container'>
        <h3 className='game-name'>{gameName}</h3>
        <p className='game-status'>Status: {gameStatus}</p>
        <p className='players-count'>Players: {playersCount}/{playersMax}</p>
        <p className='game-ends'>Ends in: {gameEnds}</p>
        <div className='game-links'>
            <a href='#'><img src={showMore} alt='Show more'/></a>
            <a href='#'><img src={closeCircle} alt='Delete'/></a>
        </div>
    </div>
  );
};

export default GameElement;