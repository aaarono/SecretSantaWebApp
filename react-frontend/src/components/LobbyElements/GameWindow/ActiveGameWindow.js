import React from 'react';
import './GameWindow.css';
import '../../../index.css';
import Button from '../../ui/Button/Button';

const ActiveGameWindow = () => {
  return (
    <div className="active-game-window">
      <h2>Ho-ho-ho! You are a Secret Santa for <br/> [PlayerName]</h2>
      <div className="gift-cards">
        <div className="gift-card">
          <h3>A big gift</h3>
          <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.Lorem Ipsum is simply dummy text.</p>
        </div>
        <div className="gift-card">
          <h3>A big gift</h3>
          <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.Lorem Ipsum is simply dummy text.</p>
        </div>
      </div>
      <div className="active-game-buttons">
        <Button text={'Gift Recieved'} type={'submit'} />
        <Button text={'Gift presented'} type={'submit'}/>
      </div>
    </div>
  );
};

export default ActiveGameWindow;
