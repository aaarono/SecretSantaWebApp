import React, { useEffect, useState, useContext, useCallback } from 'react';
import { useParams } from 'react-router-dom';
import '../../index.css';
import './LobbyPage.css';
import GameID from '../../components/LobbyElements/GameID/GameID';
import GameBanner from '../../components/LobbyElements/GameBanner/GameBanner';
import PlayersList from '../../components/LobbyElements/PlayersList/PlayersList';
import DeadlineTimer from '../../components/LobbyElements/DeadlineTimer/DeadlineTimer';
import WaitingGameWindow from '../../components/LobbyElements/GameWindow/WaitingGameWindow';
import StartGameWindow from '../../components/LobbyElements/GameWindow/StartGameWindow';
import Chat from '../../components/LobbyElements/Chat/Chat';
import { UserContext } from '../../components/contexts/UserContext';
import useWebSocket from '../../hooks/useWebSocket';
import api from '../../services/api/api';

const LobbyPage = () => {
  const { gameUuid } = useParams();
  const { user } = useContext(UserContext);
  const [players, setPlayers] = useState([]);
  const [isAuthorized, setIsAuthorized] = useState(false);
  const [gameName, setGameName] = useState('');
  const [playerCount, setPlayerCount] = useState(0);
  const [gameEndsAt, setGameEndsAt] = useState(null);
  const [creatorLogin, setCreatorLogin] = useState(null);

  const login = user.username;

  const updatePlayerList = useCallback((newPlayers) => {
    setPlayers(newPlayers);
    setPlayerCount(newPlayers.length);
  }, []);

  // Функция для установки или обновления статуса конкретного игрока по логину
  const setPlayerStatus = useCallback((playerLogin, isOnline = true) => {
    setPlayers(prevPlayers => {
      const playerIndex = prevPlayers.findIndex(p => p.login === playerLogin);
      if (playerIndex !== -1) {
        // Обновляем статус существующего игрока
        const updatedPlayers = [...prevPlayers];
        updatedPlayers[playerIndex] = { 
          ...updatedPlayers[playerIndex], 
          is_online: isOnline 
        };
        return updatedPlayers;
      } else {
        // Если игрока нет в списке, добавим его с минимальными данными
        return [...prevPlayers, { login: playerLogin, is_online: isOnline }];
      }
    });
  }, []);

  const handleWebSocketMessage = useCallback((message) => {
    console.log('WebSocket message:', message);

    switch (message.type) {
      case 'welcome':
        console.log('Received welcome message from server.');
        break;
      case 'auth_success':
        console.log('User authorized via WebSocket');
        setIsAuthorized(true);
        if (gameUuid) {
          sendMessage({ type: 'join_game', uuid: gameUuid, login });
          console.log(`Subscribed to game with ID: ${gameUuid}`);
        }
        break;
      case 'player_joined':
        // Когда новый игрок присоединяется, устанавливаем его в онлайн
        setPlayerStatus(message.login, true);
        break;
      case 'joined_game':
        console.log('joined_game message received:', message);
        if (message.players && Array.isArray(message.players)) {
          // Если сервер в этом сообщении отдаёт только логины, конвертируем их 
          // в объекты. Присваиваем is_online: true по умолчанию.
          const initialPlayers = message.players.map(plLogin => ({
            login: plLogin,
            is_online: true
          }));
          updatePlayerList(initialPlayers);
        } else {
          console.warn('joined_game message received without players array');
        }
        break;
      case 'player_left':
        // Игрок покидает игру, отметим его оффлайн
        setPlayerStatus(message.login, false);
        break;
      case 'game_deleted':
        alert(message.message);
        window.location.href = '/';
        break;
      default:
        console.warn('Unknown WebSocket message type:', message.type);
    }
  }, [gameUuid, login, updatePlayerList, setPlayerStatus]);

  const handleWebSocketOpen = useCallback((socket) => {
    console.log('WebSocket connected for lobby');
  }, []);

  const sendMessage = useWebSocket(handleWebSocketMessage, handleWebSocketOpen);

  useEffect(() => {
    if (gameUuid) {
      fetch(`http://localhost:8000/game/get?uuid=${gameUuid}`, {
        credentials: 'include',
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === 'success' && data.game) {
            console.log('Game data:', data.game);
            setGameName(data.game.name || 'Unnamed Game');
            setGameEndsAt(data.game.endsat || '');

            // Считаем, что игроки возвращаются уже как [{ login: "...", is_gifted: ... }, ...]
            // Присвоим им is_online: true по умолчанию, или сделайте логику проверки на онлайность отдельно
            const fullPlayers = (data.game.players || []).map(p => ({
              ...p,
              is_online: true // Или определяйте логику иначе
            }));
            updatePlayerList(fullPlayers);

            // Устанавливаем логин создателя из данных
            setCreatorLogin(data.game.creator_login);
          }
        })
        .catch((err) => console.error('Error fetching game data:', err));
    }
  }, [gameUuid, updatePlayerList]);

  return (
    <div className="lobby-page-container">
      <GameID gameUuid={gameUuid} />
      <GameBanner gameName={gameName} playerCount={playerCount} />
      <PlayersList players={players} creatorLogin={creatorLogin} />
      <DeadlineTimer endsAt={gameEndsAt} />
      {creatorLogin === login ? (
        <StartGameWindow
          isAuthorized={isAuthorized}
          playersCount={playerCount}
          gameUuid={gameUuid}
          api={api}
          sendMessage={sendMessage}
        />
      ) : (
        <WaitingGameWindow isAuthorized={isAuthorized} />
      )}
      <Chat gameUuid={gameUuid} />
    </div>
  );
};

export default LobbyPage;
