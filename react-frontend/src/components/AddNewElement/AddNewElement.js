import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import '../../index.css';
import './AddNewElement.css';
import { SlPlus } from "react-icons/sl";
import api from '../../services/api/api'; // Подключение API
import { login } from '../../services/api/authService';

const AddNewElement = ({ onAdd }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [wishName, setWishName] = useState('');
  const [description, setDescription] = useState('');
  const [url, setUrl] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleAddClick = () => {
    setIsOpen(true);
  };

  const handleSave = async () => {
    if (!wishName || !description || !url) {
      alert('Все поля должны быть заполнены!');
      return;
    }

    setIsLoading(true);
    try {
      const data = {
        name: wishName,
        description,
        url,
        login: null, // Логин пользователя, если нужно
      };
      const response = await api.post('/user/wishlist/create', data);

      console.log('response:', response);
      // Вызываем функцию для добавления элемента в состояние родителя
      if (response.status === 'success') {
        onAdd({
          id: response.id, // Предполагаем, что API возвращает созданный объект
          name: wishName,
          description,
          url,
        });
        alert('Элемент успешно добавлен!');
        setWishName('');
        setDescription('');
        setUrl('');
        setIsOpen(false);
      }
    } catch (error) {
      // 1. Сообщение об ошибке
      console.error('Ошибка при добавлении элемента:', error);
    
      // 2. Если это ошибка от сервера (response), логируем всю возможную информацию
      if (error.response) {
        console.error('Data:', error.response.data);
        console.error('Status:', error.response.status);
        console.error('Headers:', error.response.headers);
      }
    
      // 3. Можно также вывести stack, если нужно
      console.error('Stack:', error.stack);
    
      alert('Не удалось добавить элемент.');    
    } finally {
      setIsLoading(false);
    }
  };

  const handleCancel = () => {
    setWishName('');
    setDescription('');
    setUrl('');
    setIsOpen(false);
  };

  const dialogContent = isOpen && (
    <div className="overlay">
      <div className="dialog">
        <input
          type="text"
          placeholder="Name"
          value={wishName}
          onChange={(e) => setWishName(e.target.value)}
        />
        <textarea
          placeholder="Description"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
        />
        <textarea
          placeholder="Url"
          value={url}
          onChange={(e) => setUrl(e.target.value)}
        />
        <button onClick={handleSave} disabled={isLoading}>
          {isLoading ? 'Saving...' : 'Save'}
        </button>
        <button onClick={handleCancel} disabled={isLoading}>
          Cancel
        </button>
      </div>
    </div>
  );

  return (
    <div className="add-new-element-container">
      <SlPlus onClick={handleAddClick} />
      {ReactDOM.createPortal(dialogContent, document.body)}
    </div>
  );
};

export default AddNewElement;
