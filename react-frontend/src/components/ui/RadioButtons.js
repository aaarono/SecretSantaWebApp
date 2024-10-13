import React from 'react';
import '../../index.css';

const RadioButtons = ({
  options,
  direction = 'horizontal',
  gap = '10px',
  style,
}) => {
  const defaultStyle = {
    display: 'flex',
    flexDirection: direction === 'vertical' ? 'column' : 'row',
    gap: gap,
  };

  return (
    <div style={{ ...defaultStyle, ...style }} className="radio-buttons-container">
      {Object.entries(options).map(([label, value]) => (
        <div key={label} className="radio-button-item" style={direction === 'vertical' ? { display: 'flex', alignItems: 'center', justifyContent: 'space-between', width: '100%' } : {}}>
          <label className="radio-button-label" style={{ marginRight: '5px' }}>{label}</label>
          <input type="radio" name="radioGroup" value={value} />
        </div>
      ))}
    </div>
  );
};

export default RadioButtons;