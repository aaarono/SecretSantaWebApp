import React from 'react';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import '../../index.css';
import './SettingsPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';

const SettingsPage = () => {
  const navigate = useNavigate();

  return (
    <>
        <Logo/>
        <Header username={'VasyaPupkin228'} email={'vasyapupkin228@gmail.com'}/>
        <div className='settings-container'>
            <h2>Settings</h2>
        </div>
    </>
  );
};

export default SettingsPage;