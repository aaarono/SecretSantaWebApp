// WishlistElement.js
import React, { useState } from 'react';
import '../../index.css';
import './WishlistElement.css';
import showMore from '../../assets/showMore.svg';
import closeCircle from '../../assets/closeCircle.svg';
import { SlClose } from "react-icons/sl";
import { SlArrowRightCircle } from "react-icons/sl";

const WishlistElement = ({ wishName, description, url }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [newWishName, setNewWishName] = useState(wishName);
  const [newDescription, setNewDescription] = useState(description);
  const [newUrl, setNewUrl] = useState(url);

  const handleEditClick = () => {
    setIsEditing(true);
  };

  const handleSave = () => {
    // Логика сохранения изменений
    setIsEditing(false);
  };

  const handleCancel = () => {
    setNewWishName(wishName);
    setNewDescription(description);
    setNewUrl(url);
    setIsEditing(false);
  };

  return (
    <div className='wishlist-element-container'>
      <h3>{wishName}</h3>
      <p>{description}</p>
      <div className='wishlist-links'>
        <SlArrowRightCircle onClick={handleEditClick} />
        <SlClose />
      </div>
      {isEditing && (
        <div className='overlay'>
          <div className='edit-dialog'>
            <input
              type='text'
              value={newWishName}
              onChange={(e) => setNewWishName(e.target.value)}
            />
            <textarea
              value={newDescription}
              onChange={(e) => setNewDescription(e.target.value)}
            />
            <textarea
              value={newUrl}
              onChange={(e) => setNewUrl(e.target.value)}
            />
            <button onClick={handleSave}>Сохранить</button>
            <button onClick={handleCancel}>Отмена</button>
          </div>
        </div>
      )}
    </div>
  );
};

export default WishlistElement;