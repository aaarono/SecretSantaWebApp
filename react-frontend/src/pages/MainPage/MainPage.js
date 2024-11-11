import React from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button/Button';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import '../../index.css';
import './MainPage.css';
import MainSection from '../../components/MainSection/MainSection';

const MainPage = () => {
  const navigate = useNavigate();

  return (
    <>
        <Logo/>
        <Header username={'VasyaPupkin228'} email={'vasyapupkin228@gmail.com'}/>
        <MainSection />
    </>
  );
};

export default MainPage;