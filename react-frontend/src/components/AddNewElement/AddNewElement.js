import React, { useState } from 'react';
import '../../index.css';
import './AddNewElement.css';
import { SlPlus } from "react-icons/sl";

const AddNewElement = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [wishName, setWishName] = useState('');
  const [description, setDescription] = useState('');
  const [url, setUrl] = useState('');

  const handleAddClick = () => {
    setIsOpen(true);
  };

  const handleSave = () => {
    setIsOpen(false);
  };

  const handleCancel = () => {
    setWishName('');
    setDescription('');
    setUrl('');
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
              placeholder='Name'
              value={wishName}
              onChange={(e) => setWishName(e.target.value)}
            />
            <textarea
              placeholder='Description'
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            />
            <textarea
              placeholder='Url'
              value={url}
              onChange={(e) => setUrl(e.target.value)}
            />
            <button onClick={handleSave}>Save</button>
            <button onClick={handleCancel}>Cancel</button>
          </div>
        </div>
      )}
    </div>
  );
};

export default AddNewElement;