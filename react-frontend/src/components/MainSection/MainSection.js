import React from 'react';
import '../../index.css';
import './MainSection.css';
import GameElement from '../GameElement/GameElement';
import WishlistElement from '../WishlistElement/WishlistElement';
import AddNewElement from '../AddNewElement/AddNewElement';


const MainSection = () => {
  return (
    <div className='main-section-container'>
        <div className='main-section-games'>
            <h2>Active Games</h2>
            <div className='main-section-game-elements'>
                <GameElement gameName = {'UPCE'} gameStatus = {'Active'} playersCount = {'8'} playersMax = {'10'} gameEnds = {'14.04.2024 | 14:44'}/>
                <GameElement gameName = {'UPCE'} gameStatus = {'Active'} playersCount = {'8'} playersMax = {'10'} gameEnds = {'14.04.2024 | 14:44'}/>
                <AddNewElement/>
            </div>
        </div>
        <div className='main-section-wishlist'>
            <h2>Wishlist</h2>
            <div className='main-section-wishlist-elements'>
                <WishlistElement wishName={'PlayStation 5'} description={'Lorem ipsum dor amet. Lorem ipsum dor amet. Lorem ipsum dor amet.'}/>
                <WishlistElement wishName={'PlayStation 5'} description={'Lorem ipsum dor amet. Lorem ipsum dor amet. Lorem ipsum dor amet.'}/>
                <AddNewElement/>
            </div>
        </div>
    </div>
  );
};

export default MainSection;