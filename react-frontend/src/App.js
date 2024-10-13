import React from 'react';
import { Routes, Route } from 'react-router-dom';
import Home from './components/Home';
import Register from './components/Register';
import Login from './components/Login';
import MainPage from './components/MainPage/MainPage';

function App() {
  return (
    <Routes>
      {/* <Route path="/" element={<MainPage />} /> */}
      {/* <Route path="/register" element={<Register />} />
      <Route path="/login" element={<Login />} />
      <Route path="/auth" element={<Home/>}/> */}
    </Routes>
  );
}

export default App;
