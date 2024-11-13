import React from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './Header.css';
import { Link } from 'react-router-dom';
import defaultAvatar from '../../assets/avatar.png';
import gameHistoryIco from '../../assets/gameHistory.svg';
import wishlistIco from '../../assets/wishlist.svg';
import settingsIco from '../../assets/settings.svg';
import Button from '../ui/Button/Button';

const Header = ({ username, email }) => {
    const navigate = useNavigate();
  return (
    <div className='header-container'>
        <div className='section-container-header'>
            <img className='avatar' src={defaultAvatar} alt="Avatar" />
            <div className='header-user-info'>
                <h3>{username}</h3>
                <p>{email}</p>
            </div>
            <div className='links-header'>
                <Link to="/settings" className="logo-link"><img className='settings' src={settingsIco} alt="Settings" /></Link>
            </div>
        </div>
        <div className='button-container-header'>
            <Button text={'New Game'} onClick={() => navigate('/new')}/>
            <Button text={'Connect'} onClick={() => navigate('/connect')}/>
        </div>
    </div>
  );
};

export default Header;