import React, {
  useEffect,
  useState,
  useContext,
  useCallback,
} from "react";
import { useParams } from "react-router-dom";
import "../../index.css";
import "./LobbyPage.css";
import GameID from "../../components/LobbyElements/GameID/GameID";
import GameBanner from "../../components/LobbyElements/GameBanner/GameBanner";
import PlayersList from "../../components/LobbyElements/PlayersList/PlayersList";
import DeadlineTimer from "../../components/LobbyElements/DeadlineTimer/DeadlineTimer";
import WaitingGameWindow from "../../components/LobbyElements/GameWindow/WaitingGameWindow";
import StartGameWindow from "../../components/LobbyElements/GameWindow/StartGameWindow";
import Chat from "../../components/LobbyElements/Chat/Chat";
import { UserContext } from "../../components/contexts/UserContext";
import useWebSocket from "../../hooks/useWebSocket";
import api from "../../services/api/api"; // <-- используем ваш ApiService
import ActiveGameWindow from "../../components/LobbyElements/GameWindow/ActiveGameWindow";

const LobbyPage = () => {
  const { gameUuid } = useParams();
  const { user } = useContext(UserContext);

  const [players, setPlayers] = useState([]);
  const [isAuthorized, setIsAuthorized] = useState(false);
  const [gameName, setGameName] = useState("");
  const [gameEndsAt, setGameEndsAt] = useState(null);
  const [creatorLogin, setCreatorLogin] = useState(null);
  const [chatMessages, setChatMessages] = useState([]);
  const [gameStatus, setGameStatus] = useState("pending");

  const login = user?.username;

  // Колбэк для обновления списка игроков
  const updatePlayerList = useCallback((newPlayers) => {
    setPlayers(newPlayers);
  }, []);

  // Колбэк для изменения статуса игрока (online/offline)
  const setPlayerStatus = useCallback((playerLogin, isOnline = true) => {
    setPlayers((prevPlayers) => {
      const playerIndex = prevPlayers.findIndex((p) => p.login === playerLogin);
      if (playerIndex !== -1) {
        const updatedPlayers = [...prevPlayers];
        updatedPlayers[playerIndex] = {
          ...updatedPlayers[playerIndex],
          is_online: isOnline,
        };
        return updatedPlayers;
      } else {
        // Если игрока ещё нет в списке, добавим
        return [...prevPlayers, { login: playerLogin, is_online: isOnline }];
      }
    });
  }, []);

  // Обрабатываем входящие сообщения от WebSocket
  const handleWebSocketMessage = useCallback(
    (message) => {
      switch (message.type) {
        case "welcome":
          break;
        case "auth_success":
          setIsAuthorized(true);
          if (gameUuid) {
            sendMessage({ type: "join_game", uuid: gameUuid, login });
          }
          break;
        case "player_joined":
          setPlayerStatus(message.login, true);
          break;
        case "joined_game":
          if (message.players && Array.isArray(message.players)) {
            const initialPlayers = message.players.map((plLogin) => ({
              login: plLogin,
              is_online: true,
            }));
            updatePlayerList(initialPlayers);
            setChatMessages(message.messages);
          }
          break;
        case "player_left":
          setPlayerStatus(message.login, false);
          break;
        case "game_deleted":
          alert(message.message);
          window.location.href = "/";
          break;
        case "chat_message":
          if (message.gameUuid === gameUuid) {
            const newMessage = {
              login: message.login,
              message: message.message,
            };
            setChatMessages((prevMessages) => [...prevMessages, newMessage]);
          }
          break;

        case "game_started":
          // Игра переведена в статус "running"
          setGameStatus("running");
          break;

        default:
          console.warn("Unknown message type:", message.type);
          break;
      }
    },
    [gameUuid, login, updatePlayerList, setPlayerStatus]
  );

  const handleWebSocketOpen = useCallback((socket) => {
  }, []);

  const sendMessage = useWebSocket(handleWebSocketMessage, handleWebSocketOpen);

  // Загружаем данные о игре (название, статус, игроки и т.д.)
  useEffect(() => {
    if (gameUuid) {
      // Раньше было fetch("http://localhost:8000/game/get?uuid=..."),
      // теперь используем api.get:
      // Предположим, что ваш ApiService уже добавляет credentials: 'include'
      // Если нет, можно указать явно вторым параметром headers/конфиг
      api
        .get(`/game/get?uuid=${gameUuid}`)
        .then((data) => {
          if (data.status === "success" && data.game) {
            setGameName(data.game.name || "Unnamed Game");
            setGameEndsAt(data.game.endsat || "");
            setGameStatus(data.game.status);

            // Формируем список игроков
            const fullPlayers = (data.game.players || []).map((p) => ({
              ...p,
              is_online: true, // можно считать их online
            }));
            updatePlayerList(fullPlayers);

            setCreatorLogin(data.game.creator_login);
          } else {
            console.error("Game data response error:", data);
          }
        })
        .catch((err) =>
          console.error("Error fetching game data via ApiService:", err)
        );
    }
  }, [gameUuid, updatePlayerList]);

  // Определяем, какое окно отобразить (зависит от gameStatus)
  let windowToRender;
  if (gameStatus === "running" || gameStatus === "ended") {
    // Если игра запущена/завершена, показываем активное окно
    windowToRender = (
      <ActiveGameWindow gameUuid={gameUuid} gameStatus={gameStatus} />
    );
  } else {
    // Если игра ещё не начата (pending), смотрим: пользователь - создатель или нет
    if (creatorLogin && creatorLogin === login) {
      windowToRender = (
        <StartGameWindow
          isAuthorized={isAuthorized}
          playersCount={players.length}
          gameUuid={gameUuid}
          api={api}
          sendMessage={sendMessage}
        />
      );
    } else {
      windowToRender = <WaitingGameWindow isAuthorized={isAuthorized} />;
    }
  }

  return (
    <div className="lobby-page-container">
      <GameID gameUuid={gameUuid} />
      <GameBanner gameName={gameName} playerCount={players.length} />
      <PlayersList players={players} creatorLogin={creatorLogin} />
      <DeadlineTimer endsAt={gameEndsAt} />
      {windowToRender}
      <Chat
        gameUuid={gameUuid}
        sendMessage={sendMessage}
        messages={chatMessages}
      />
    </div>
  );
};

export default LobbyPage;
