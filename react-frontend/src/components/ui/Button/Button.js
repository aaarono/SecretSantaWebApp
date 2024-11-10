import React from 'react';
import '../../../index.css';
import './Button.css';

const Button = ({ text, onClick, type }) => {
  return (
    <button 
      className="button-main"
      type={type}
      onClick={onClick}
    >
      {text}
    </button>
  );
};

export default Button;