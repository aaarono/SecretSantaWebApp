import React from 'react';
import './GameBanner.css';
import '../../../index.css';

const GameBanner = ({ gameName, playerCount }) => {
  return (
    <div className="game-banner">
      <h2>{gameName}</h2>
      <p>{playerCount} players</p>
    </div>
  );
};

export default GameBanner;
