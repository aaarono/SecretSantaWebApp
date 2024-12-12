// src/components/Layout/Layout.jsx
import React from 'react';
import Logo from '../Logo/Logo';
import Header from '../Header/Header';
import { Outlet } from 'react-router-dom'; // Для рендера вложенных маршрутов

const Layout = () => {
  return (
    <>
      <Logo />
      <Header username={'VasyaPupkin228'} email={'vasyapupkin228@gmail.com'} />
      <>
        <Outlet />
      </>
    </>
  );
};

export default Layout;
