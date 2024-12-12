import React, { useState } from 'react';
import '../../index.css';
import './AddNewElement.css';
import { SlPlus } from "react-icons/sl";

const AddNewElement = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [wishName, setWishName] = useState('');
  const [description, setDescription] = useState('');

  const handleAddClick = () => {
    setIsOpen(true);
  };

  const handleSave = () => {
    // Логика сохранения нового элемента
    setIsOpen(false);
  };

  const handleCancel = () => {
    setWishName('');
    setDescription('');
    setIsOpen(false);
  };

  return (
    <div className='add-new-element-container'>
      <SlPlus onClick={handleAddClick} />
      {isOpen && (
        <div className='overlay'>
          <div className='dialog'>
            <input
              type='text'
              placeholder='Название'
              value={wishName}
              onChange={(e) => setWishName(e.target.value)}
            />
            <textarea
              placeholder='Описание'
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            />
            <button onClick={handleSave}>Добавить</button>
            <button onClick={handleCancel}>Отмена</button>
          </div>
        </div>
      )}
    </div>
  );
};

export default AddNewElement;