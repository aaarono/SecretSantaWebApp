import React from 'react';
import { useNavigate } from 'react-router-dom';

function Home() {
  const navigate = useNavigate();

  return (
    <div className="container">
      <h1 className="title">Добро пожаловать!</h1>
      <div className="button-group">
        <button onClick={() => navigate('/register')} className="button">
          Регистрация
        </button>
        <button onClick={() => navigate('/login')} className="button">
          Вход
        </button>
      </div>
    </div>
  );
}

export default Home;
