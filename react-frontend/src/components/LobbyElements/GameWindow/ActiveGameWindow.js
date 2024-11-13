import React from 'react';
import './GameWindow.css';
import '../../../index.css';

const ActiveGameWindow = () => {
  return (
    <div className="game-window">
      <h2>Ho-ho-ho! You are a Secret Santa for [PlayerName]</h2>
      <div className="gift-cards">
        <div className="gift-card">
          <h3>A big gift</h3>
          <p>Описание подарка...</p>
        </div>
        <div className="gift-card">
          <h3>A big gift</h3>
          <p>Описание подарка...</p>
        </div>
      </div>
      <button className="present-button">I’ve received a present</button>
    </div>
  );
};

export default ActiveGameWindow;
