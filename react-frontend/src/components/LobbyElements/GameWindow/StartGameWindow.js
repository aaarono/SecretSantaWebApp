import React, { useState } from 'react';
import './GameWindow.css';
import '../../../index.css';
import Button from '../../ui/Button/Button';

const StartGameWindow = ({ isAuthorized, playersCount, gameUuid, api, sendMessage }) => {
  const [showConfirm, setShowConfirm] = useState(false); // Состояние для диалогового окна удаления игры

  const handleStartGame = async () => {
    try {
      const response = await api.put('/game/update', {
        uuid: gameUuid,
        status: 'running',
      });
      if (response.status === 'success') {
        alert('Game started successfully!');
        // Отправка уведомления через WebSocket
        sendMessage({
          type: 'game_started',
          uuid: gameUuid,
        });
      } else {
        alert('Failed to start the game.');
      }
    } catch (error) {
      console.error('Error starting game:', error);
    }
  };

  const handleDeleteGame = async () => {
    try {
      console.log('Deleting game:', gameUuid);
      const response = await api.delete('/game/delete', { uuid: gameUuid });
      if (response.status === 'success') {
        // Отправка сообщения об удалении игры через WebSocket
        sendMessage({
          type: 'delete_game',
          uuid: gameUuid,
        });
        alert('Game deleted successfully!');
      } else {
        alert('Failed to delete the game.');
      }
    } catch (error) {
      console.error('Error deleting game:', error);
    }
  };

  return (
    <div className="game-window">
      {isAuthorized && playersCount > 2 ? (
        <Button text={"Start Game"} type={"button"} onClick={handleStartGame} />
      ) : (
        <h2>Waiting for players...</h2>
      )}
      <Button
        text={"Delete Game"}
        type={"button"}
        onClick={() => setShowConfirm(true)} // Показать диалог
      />
      {showConfirm && (
        <div className="confirm-dialog">
          <p>Are you sure you want to delete the game?</p>
          <Button text={"Yes"} type={"button"} onClick={handleDeleteGame} />
          <Button text={"No"} type={"button"} onClick={() => setShowConfirm(false)} />
        </div>
      )}
    </div>
  );
};

export default StartGameWindow;
