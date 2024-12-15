import React from 'react';
import './PlayersList.css';

const PlayersList = ({ players, creatorLogin }) => {
  return (
    <div className="players-list">
      <h3>Players</h3>
      <ul>
        {players.map((player, index) => (
          <li key={index} className="player-item">
            <span>
              {index + 1}. {player.login}
            </span>
            {player.login === creatorLogin && (
              <span className="santa-icon" title="Creator">🎅</span>
            )}
            {player.is_online ? (
              <span className="online-status" title="Online">🟢</span>
            ) : (
              <span className="offline-status" title="Offline">🔴</span>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default PlayersList;
