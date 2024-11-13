import React from 'react';
import './GameWindow.css';
import '../../../index.css';
import Button from '../../ui/Button/Button';

const StartGameWindow = () => {
  return (
    <div className="game-window">
      <Button text={"Start Game"} type={"submit"} />
    </div>
  );
};

export default StartGameWindow;
