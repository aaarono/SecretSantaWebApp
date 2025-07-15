import { useEffect, useRef, useContext } from 'react';
import { UserContext } from '../components/contexts/UserContext';

const useWebSocket = (onMessage, onOpen, onClose, onError) => {
  const socketRef = useRef(null);
  const { user } = useContext(UserContext); // Получаем данные из контекста UserContext

  const FIXED_URL = 'ws://16.171.60.26/ws/'; // Задаем статичный URL

  useEffect(() => {
    if (!user?.username) { // Изменено с user.login на user.username
      console.error('Username is required for WebSocket connection');
      return;
    }

    // Создаем WebSocket соединение
    const socket = new WebSocket(WS_URL);

    socket.onopen = () => {
      console.log('WebSocket connected:', WS_URL);
      if (onOpen) onOpen(socket);

      // Отправляем сообщение `auth` с логином после открытия соединения
      const authMessage = { type: 'auth', login: user.username }; // Изменено с user.login на user.username
      socket.send(JSON.stringify(authMessage));
      console.log('Auth message sent:', authMessage);
    };

    socket.onmessage = (event) => {
      try {
        const message = JSON.parse(event.data);
        console.log('WebSocket message received:', message);
        if (onMessage) onMessage(message);
      } catch (error) {
        console.error('Error parsing WebSocket message:', error);
      }
    };

    socket.onclose = (event) => {
      console.log('WebSocket disconnected:', WS_URL);
      if (onClose) onClose(event);
    };

    socket.onerror = (error) => {
      console.error('WebSocket error:', error);
      if (onError) onError(error);
    };

    socketRef.current = socket;

  
    return () => {
      if (socket) {
        socket.close();
      }
    };
  }, [user, onMessage, onOpen, onClose, onError]);

  const sendMessage = (message) => {
    if (socketRef.current && socketRef.current.readyState === WebSocket.OPEN) {
      socketRef.current.send(JSON.stringify(message));
      console.log('WebSocket message sent:', message);
    } else {
      console.error('WebSocket is not open. Message not sent:', message);
    }
  };

  return sendMessage;
};

export default useWebSocket;
