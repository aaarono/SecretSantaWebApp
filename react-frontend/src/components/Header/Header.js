import React from 'react';
import '../../index.css';
import './Header.css';
import defaultAvatar from '../../assets/avatar.png';
import gameHistoryIco from '../../assets/gameHistory.svg';
import wishlistIco from '../../assets/wishlist.svg';
import settingsIco from '../../assets/settings.svg';
import Button from '../ui/Button/Button';

const Header = ({ username, email }) => {
  return (
    <div className='header-container'>
        <div className='section-container-header'>
            <img className='avatar' src={defaultAvatar} alt="Avatar" />
            <div className='header-user-info'>
                <h3>{username}</h3>
                <p>{email}</p>
            </div>
            <div className='links-header'>
                {/* <a href='#'><img className='gameHistory' src={gameHistoryIco} alt="Games History" /></a>
                <a href='#'><img className='wishlist' src={wishlistIco} alt="Wishlist" /></a> */}
                <a href='#'><img className='settings' src={settingsIco} alt="Settings" /></a>
            </div>
        </div>
        <div className='button-container-header'>
            <Button text={'New Game'} />
            <Button text={'Connect'} />
        </div>
    </div>
  );
};

export default Header;