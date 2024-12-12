import React from 'react';
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
  value,
  onChange,
  showErrors, // Добавляем проп для контроля отображения ошибок
}) => {
  const isValid = errorCheck ? errorCheck(value) : true;

  return (
    <div className="text-input-container">
      {label && <label className="text-input-label">{label}</label>}
      <input
        name={name}
        type={type}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        style={{ ...style }}
        className={`text-input ${!isValid && showErrors ? 'text-input-error' : ''}`}
      />
      {!isValid && showErrors && errorText && <div className="error-tooltip">{errorText}</div>}
    </div>
  );
};

export default TextInput;
