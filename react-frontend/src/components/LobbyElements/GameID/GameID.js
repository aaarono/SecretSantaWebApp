import React, { useState } from 'react';
import './GameID.css';

const GameID = ({ gameUuid }) => {
  const [isCopied, setIsCopied] = useState(false);

  const handleCopy = () => {
    if (gameUuid) {
      navigator.clipboard.writeText(gameUuid);
      setIsCopied(true);
      setTimeout(() => setIsCopied(false), 2000);
    }
  };

  return (
    <div className={`game-id ${isCopied ? 'copied' : ''}`} onClick={handleCopy}>
      <h3>{isCopied ? 'Copied!' : 'Copy Game ID'}</h3>
      <p>{gameUuid || 'No game selected'}</p>
    </div>
  );
};

export default GameID;
