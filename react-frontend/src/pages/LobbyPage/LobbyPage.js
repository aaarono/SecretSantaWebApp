import React, { useContext, useState, useEffect } from 'react';
import { GameContext } from '../../components/contexts/GameContext';
import useWebSocket from '../../hooks/useWebSocket';
import '../../index.css';
import './LobbyPage.css';
import GameID from '../../components/LobbyElements/GameID/GameID';
import GameBanner from '../../components/LobbyElements/GameBanner/GameBanner';
import PlayersList from '../../components/LobbyElements/PlayersList/PlayersList';
import DeadlineTimer from '../../components/LobbyElements/DeadlineTimer/DeadlineTimer';
import WaitingGameWindow from '../../components/LobbyElements/GameWindow/WaitingGameWindow';
import { UserContext } from '../../components/contexts/UserContext';

const LobbyPage = () => {
  const { gameId } = useContext(GameContext); // Получаем ID игры из контекста
  const { user } = useContext(UserContext);
  const [players, setPlayers] = useState([]); // Список игроков
  const [isAuthorized, setIsAuthorized] = useState(false); // Статус авторизации

  // Статичный логин пользователя
  const login = user.username;

  const handleWebSocketMessage = (message) => {
    console.log('WebSocket message:', message);

    // Обработка сообщений WebSocket
    switch (message.type) {
      case 'auth_success':
        console.log('User authorized via WebSocket');
        setIsAuthorized(true);
        break;
      case 'player_joined':
        setPlayers((prev) => [...prev, message.login]);
        break;
      case 'player_left':
        setPlayers((prev) => prev.filter((p) => p !== message.login));
        break;
      default:
        console.warn('Unknown WebSocket message type:', message.type);
    }
  };

  const handleWebSocketOpen = (socket) => {
    console.log('WebSocket connected for lobby');
    socket.send(JSON.stringify({ type: 'auth', login })); // Отправляем авторизацию
    if (gameId) {
      socket.send(JSON.stringify({ type: 'join_game', uuid: gameId , login})); // Присоединяемся к игре
      console.log(`Subscribed to game with ID: ${gameId}`);
    }
  };

  // Инициализация WebSocket
  const sendMessage = useWebSocket(handleWebSocketMessage, handleWebSocketOpen);

  useEffect(() => {
    if (!gameId) {
      console.warn('No game ID provided. WebSocket may not function correctly.');
    }
  }, [gameId]);

  return (
    <div className="lobby-page-container">
      <GameID />
      <GameBanner />
      <PlayersList players={players} /> {/* Передаем список игроков */}
      <DeadlineTimer />
      <WaitingGameWindow />
      {/* <StartGameWindow />
      <ActiveGameWindow /> */}
    </div>
  );
};

export default LobbyPage;
