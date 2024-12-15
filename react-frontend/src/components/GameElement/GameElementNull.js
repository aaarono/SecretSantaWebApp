import React from 'react';
import '../../index.css';
import './GameElement.css';
import { SlPlus } from "react-icons/sl";

const GameElementNull = ({ onAddGame }) => {
  return (
    <div className="add-new-element-container" onClick={onAddGame} style={{ cursor: 'pointer' }}>
      <SlPlus />
    </div>
  );
};

export default GameElementNull;
