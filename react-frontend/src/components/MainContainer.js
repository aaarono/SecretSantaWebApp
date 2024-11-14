import React from 'react';
import { useContext } from 'react';
import '../index.css';
import { ThemeContext } from './ThemeContext';

const MainContainer = ({ children }) => {
  const { isLight } = useContext(ThemeContext);
  return (
    <div className="theme-switch" data-theme={isLight ? "light" : "dark"}>
      <div className="main-container">
              {children}
      </div>
    </div>
  );
};

export default MainContainer;