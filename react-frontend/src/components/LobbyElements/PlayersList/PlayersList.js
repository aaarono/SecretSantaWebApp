import React from 'react';
import './PlayersList.css';
import '../../../index.css';

const PlayersList = () => {
  return (
    <div className="players-list">
      <h3>Players</h3>
      <ul>
        <li>1. VasyaPupkin228 🎅</li>
        <li>2. AntonGandon</li>
        <li>3. Pokemon1337</li>
        {/* Добавьте другие имена игроков */}
      </ul>
    </div>
  );
};

export default PlayersList;
