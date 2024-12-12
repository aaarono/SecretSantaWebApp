import React, { useContext, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../index.css';
import './Header.css';
import { Link } from 'react-router-dom';
import { IoSettingsOutline } from "react-icons/io5";
import Button from '../ui/Button/Button';
import Toggle from '../ui/Toggle/Toggle';
import { ThemeContext } from '../ThemeContext';
import { AvatarContext } from '../contexts/AvatarContext';
import { UserContext } from '../contexts/UserContext'; // Подключаем контекст пользователя
import api from '../../services/api/api';

const Header = () => {
    const navigate = useNavigate();
    const { isLight, toggleTheme } = useContext(ThemeContext);
    const { avatar, setAvatar } = useContext(AvatarContext); // Аватар контекст
    const { user } = useContext(UserContext); // Данные пользователя

    useEffect(() => {
        const fetchUserImage = async () => {
            try {
                const response = await api.get('/user/get-image');
                if (response.status === 'success' && response.image) {
                    setAvatar(response.image);
                }
            } catch (error) {
                console.error('Failed to fetch user image:', error);
            }
        };

        fetchUserImage();
    }, [setAvatar]);

    return (
        <div className='header-container'>
            <div className='section-container-header'>
                <img className='avatar' src={avatar} alt="Avatar" />
                <div className='header-user-info'>
                    <h3>{user.username}</h3>
                    <p>{user.email}</p>
                </div>
                <div className='links-header'>
                    <Toggle isChecked={isLight} handleChange={toggleTheme} />
                    <Link to="/settings"><IoSettingsOutline className="settings-link" /></Link>
                </div>
            </div>
            <div className='button-container-header'>
                <Button text={'New Game'} onClick={() => navigate('/new')} />
                <Button text={'Connect'} onClick={() => navigate('/connect')} />
            </div>
        </div>
    );
};

export default Header;
