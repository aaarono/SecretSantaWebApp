import React from 'react';
import { Routes, Route } from 'react-router-dom';
import MainPage from './pages/MainPage'
import LoginForm from './pages/LoginPage';
import RegistrationForm from './pages/RegistrationPage';

function App() {
  return (
    <Routes>
      <Route path="/" element={<MainPage />} />
      <Route path="/login" element={<LoginForm />} />
      <Route path="/registration" element={<RegistrationForm />} />
    </Routes>
  );
}

export default App;
