import React from 'react';
import './PlayersList.css';

const PlayersList = ({ players }) => {
  return (
    <div className="players-list">
      <h3>Players</h3>
      <ul>
        {players.map((player, index) => (
          <li key={index}>
            {index + 1}. {player}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default PlayersList;
