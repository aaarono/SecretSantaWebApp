import React from 'react';
import '../../index.css'; // Styles will be applied from index.css

const Button = ({ text, onClick, style }) => {
  return (
    <button 
      className="custom-button"
      onClick={onClick}
      style={style}
    >
      {text}
    </button>
  );
};

export default Button;