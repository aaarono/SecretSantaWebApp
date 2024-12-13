import React, { useContext, useState } from 'react';
import { GameContext } from '../../contexts/GameContext';
import './GameID.css';

const GameID = () => {
  const { gameId } = useContext(GameContext);
  const [isCopied, setIsCopied] = useState(false);

  const handleCopy = () => {
    if (gameId) {
      navigator.clipboard.writeText(gameId);
      setIsCopied(true);
      setTimeout(() => setIsCopied(false), 2000); // Убираем сообщение через 2 секунды
    }
  };

  return (
    <div className={`game-id ${isCopied ? 'copied' : ''}`} onClick={handleCopy}>
      <h3>{isCopied ? 'Copied!' : 'Copy Game ID'}</h3>
      <p>{gameId || 'No game selected'}</p>
    </div>
  );
};

export default GameID;
