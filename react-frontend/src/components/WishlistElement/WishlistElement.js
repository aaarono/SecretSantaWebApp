import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import '../../index.css';
import './WishlistElement.css';
import { SlClose, SlArrowRightCircle } from "react-icons/sl";
import api from '../../services/api/api'; // Подключение API

const WishlistElement = ({ id, wishName, description, url, onDelete }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [newWishName, setNewWishName] = useState(wishName);
  const [newDescription, setNewDescription] = useState(description);
  const [newUrl, setNewUrl] = useState(url);
  const [isLoading, setIsLoading] = useState(false);

  const handleEditClick = () => {
    setIsEditing(true);
  };

  const handleSave = async () => {
    setIsLoading(true);
    try {
      const data = {
        id,
        name: newWishName,
        description: newDescription,
        url: newUrl,
      };
      console.log('data:', data);
      await api.put('/user/wishlist/update', data);
      alert('Желание успешно обновлено!');
      setIsEditing(false);
    } catch (error) {
      console.error('Ошибка при обновлении желания:', error);
      alert('Не удалось обновить желание.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleDelete = async () => {
    if (!window.confirm('Вы уверены, что хотите удалить это желание?')) {
      return;
    }

    setIsLoading(true);
    try {
      console.log('id:', id);
      await api.delete('/user/wishlist/delete', { id });
      alert('Желание успешно удалено!');
      onDelete(id); // Уведомляем родительский компонент об удалении
    } catch (error) {
      console.error('Ошибка при удалении желания:', error);
      alert('Не удалось удалить желание.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleCancel = () => {
    setNewWishName(wishName);
    setNewDescription(description);
    setNewUrl(url);
    setIsEditing(false);
  };

  const editDialog = (
    <div className="overlay">
      <div className="edit-dialog">
        <input
          type="text"
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
        <button onClick={handleSave} disabled={isLoading}>
          {isLoading ? 'Saving...' : 'Сохранить'}
        </button>
        <button onClick={handleCancel} disabled={isLoading}>
          Отмена
        </button>
      </div>
    </div>
  );

  return (
    <div className="wishlist-element-container">
      <h3>{wishName}</h3>
      <p>{description}</p>
      <div className="wishlist-links">
        <SlArrowRightCircle onClick={handleEditClick} />
        <SlClose onClick={handleDelete} />
      </div>
      {isEditing && ReactDOM.createPortal(editDialog, document.body)}
    </div>
  );
};

export default WishlistElement;
