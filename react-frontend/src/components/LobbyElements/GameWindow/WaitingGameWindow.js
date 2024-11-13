import React from 'react';
import './GameWindow.css';
import '../../../index.css';

const WaitingGameWindow = () => {
  return (
    <div className="game-window">
      <button className="start-game-button">Waiting for players...</button>
    </div>
  );
};

export default WaitingGameWindow;
