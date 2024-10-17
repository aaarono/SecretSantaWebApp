import React, { useState } from 'react';
import '../../../index.css';
import './TextInput.css';

const TextInput = ({
  name,
  type = 'text',
  label,
  placeholder,
  errorCheck,
  errorText,
  style,
}) => {
  const [value, setValue] = useState('');
  const [error, setError] = useState(false);

  const handleChange = (e) => {
    const inputValue = e.target.value;
    setValue(inputValue);
    if (errorCheck) {
      setError(!errorCheck(inputValue));
    }
  };

  return (
    <div className="text-input-container">
      {label && <label className="text-input-label">{label}</label>}
      <input
        name={name} 
        type={type}
        value={value}
        onChange={handleChange}
        placeholder={placeholder}
        style={{ ...{}, ...style }}
        className="text-input"
      />
      {error && errorText && <div className="error-tooltip">{errorText}</div>}
    </div>
  );
};

export default TextInput;