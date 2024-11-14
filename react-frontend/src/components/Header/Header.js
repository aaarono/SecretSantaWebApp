import React, { useContext } from 'react';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './Header.css';
import { Link } from 'react-router-dom';
import defaultAvatar from '../../assets/avatar.png';
import settingsIco from '../../assets/settings.svg';
import Button from '../ui/Button/Button';
import Toggle from '../ui/Toggle/Toggle';
import { ThemeContext } from '../ThemeContext';
import { IoSettingsOutline } from "react-icons/io5";

const Header = ({ username, email }) => {
    const navigate = useNavigate();
    const { isLight, toggleTheme } = useContext(ThemeContext);
  return (
    <div className='header-container'>
        <div className='section-container-header'>
            <img className='avatar' src={defaultAvatar} alt="Avatar" />
            <div className='header-user-info'>
                <h3>{username}</h3>
                <p>{email}</p>
            </div>
            <div className='links-header'>
                <Toggle isChecked={isLight} handleChange={toggleTheme} />
                <Link to="/settings"><IoSettingsOutline className="settings-link"/></Link>
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