import React, { useEffect, useState, useContext, useCallback, response } from 'react';
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
  const [gameCreator, setGameCreator] = useState(false);

  const login = user.username;

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
      case 'joined_game':
        if (message.players && Array.isArray(message.players)) {
          setPlayers(message.players);
          setPlayerCount(message.players.length);
        } else {
          console.warn('joined_game message received without players array');
        }
        break;
      case 'player_joined':
        setPlayers((prev) => {
          const newPlayers = [...prev, message.login];
          setPlayerCount(newPlayers.length);
          return newPlayers;
        });
        break;
      case 'player_left':
        setPlayers((prev) => {
          const newPlayers = prev.filter((p) => p !== message.login);
          setPlayerCount(newPlayers.length);
          return newPlayers;
        });
      case 'game_deleted':
        alert(message.message);
        window.location.href = '/'; 
        break;
      default:
        console.warn('Unknown WebSocket message type:', message.type);
    }
  }, [gameUuid, login]);

  const handleWebSocketOpen = useCallback((socket) => {
    console.log('WebSocket connected for lobby');
    // НЕ відправляємо тут нічого, auth вже відправляється у useWebSocket
  }, []);

  // Викликаємо useWebSocket ПІСЛЯ того, як оголосили handleWebSocketMessage і handleWebSocketOpen
  const sendMessage = useWebSocket(handleWebSocketMessage, handleWebSocketOpen);

  useEffect(() => {
    if (gameUuid) {
      fetch(`http://localhost:8000/game/get?uuid=${gameUuid}`, {
        credentials: 'include',
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === 'success' && data.game) {
            setPlayers(data.game.players || []);
            setGameName(data.game.name || 'Unnamed Game');
            setPlayerCount((data.game.players || []).length);
            setGameEndsAt(data.game.endsAt || ''); // Передаємо час завершення
          }
        })
        .catch((err) => console.error('Error fetching game data:', err));
    }
  
    fetchGameCreator();
  }, [gameUuid]);
  

  const fetchGameCreator = async () => {
    const response = await api.get(`/game/player/creator?uuid=${gameUuid}`);
    console.log('Response from server:', response.creator);
    if (response.status === 'success') {
      setGameCreator(response.creator);
    }
  }; 

  // const handleBeforeUnload = async () => {
  //   if (gameUuid && login) {
  //     sendMessage({
  //       type: 'leave_game',
  //       uuid: gameUuid,
  //       login,
  //     });
  //   }
  // };

  // useEffect(() => {

  
  //   window.addEventListener('beforeunload', handleBeforeUnload);

  // });
  


  return (
    <div className="lobby-page-container">
      <GameID gameUuid={gameUuid} />
      <GameBanner gameName={gameName} playerCount={playerCount} />
      <PlayersList players={players} />
      <DeadlineTimer endsAt={gameEndsAt} />
      {gameCreator ? (
      <StartGameWindow
        isAuthorized={isAuthorized}
        playersCount={playerCount}
        gameUuid={gameUuid}
        api={api}
        sendMessage={sendMessage} // Передаем WebSocket функцию
      />
    ) : (
      <WaitingGameWindow isAuthorized={isAuthorized} />
    )}
      <Chat gameUuid={gameUuid} />
    </div>
  );
};

export default LobbyPage;
