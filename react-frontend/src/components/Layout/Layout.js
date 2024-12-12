// src/components/Layout/Layout.jsx
import React from 'react';
import Logo from '../Logo/Logo';
import Header from '../Header/Header';
import { Outlet } from 'react-router-dom'; // Для рендера вложенных маршрутов

const Layout = () => {
  return (
    <>
      <Logo />
      <Header/>
      <>
        <Outlet />
      </>
    </>
  );
};

export default Layout;
