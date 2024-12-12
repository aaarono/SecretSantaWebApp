// src/App.jsx
import React from 'react';
import { Routes, Route } from 'react-router-dom';
import WelcomePage from './pages/WelcomePage/WelcomePage';
import LoginForm from './pages/LoginPage/LoginPage';
import RegistrationForm from './pages/RegistrationPage/RegistrationPage';
import MainPage from './pages/MainPage/MainPage';
import SettingsPage from './pages/SettingsPage/SettingsPage';
import NewGamePage from './pages/NewGamePage/NewGamePage';
import ConnectPage from './pages/ConnectPage/ConnectPage';
import LobbyPage from './pages/LobbyPage/LobbyPage';
import PrivateRoute from './components/PrivateRoute/PrivateRoute'; // Убедитесь, что путь правильный

function App() {
  return (
    <Routes>
      <Route path="/auth" element={<WelcomePage />} />
      <Route path="/login" element={<LoginForm />} />
      <Route path="/registration" element={<RegistrationForm />} />
      {/* Родительский маршрут для защищённых маршрутов */}
      <Route element={<PrivateRoute />}>
        <Route path="/" element={<MainPage />} />
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="/new" element={<NewGamePage />} />
        <Route path="/connect" element={<ConnectPage />} />
        <Route path="/lobby" element={<LobbyPage />} />
      </Route>

      {/* Опционально: обработка несуществующих маршрутов */}
      <Route path="*" element={<div>404 Not Found</div>} />
    </Routes>
  );
}

export default App;
