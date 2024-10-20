import React from 'react';
import '../../../index.css';
import './Button.css';

const Button = ({ text, onClick }) => {
  return (
    <button 
      className="button-main"
      onClick={onClick}
    >
      {text}
    </button>
  );
};

export default Button;