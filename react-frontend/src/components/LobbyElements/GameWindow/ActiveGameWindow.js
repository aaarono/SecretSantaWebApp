import React, { useState, useEffect } from 'react';
import './GameWindow.css';
import '../../../index.css';
import Button from '../../ui/Button/Button';
import api from '../../../services/api/api'; // импорт api сервиса
import GiftList from '../GiftList/GiftList'; // импорт компонента GiftList
import FinishedGame from './FinishedGame'; // импорт компонента завершённой игры

const ActiveGameWindow = ({ gameUuid, gameStatus }) => {
  const [receiver, setReceiver] = useState(null);
  const [userIsGifted, setUserIsGifted] = useState(false);
  const [receiverIsGifted, setReceiverIsGifted] = useState(false);
  const [wishlistItems, setWishlistItems] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [loading, setLoading] = useState(true);
  const [gameEnded, setGameEnded] = useState(gameStatus);

  // Получаем пару (кому дарим подарок)
  useEffect(() => {
    if (!gameUuid) return;

    async function fetchPair() {
      try {
        const data = await api.get(`/game/player/pair?uuid=${gameUuid}`);
        if (data.status === 'success') {
          setReceiver(data.receiver);
          setUserIsGifted(data.user_is_gifted);
          setReceiverIsGifted(data.receiver_is_gifted);
        } else {
          console.error('Failed to get pair info:', data.message);
        }
      } catch (err) {
        console.error('Error fetching pair info:', err);
      }
    }

    fetchPair();
  }, [gameUuid]);

  // Получаем список желаний получателя
  useEffect(() => {
    if (!receiver) {
      setLoading(false);
      return;
    }

    async function fetchWishlist() {
      try {
        const data = await api.get(`/user/wishlist/user?login=${receiver}`);
        if (data.status === 'success') {
          setWishlistItems(data.wishlists || []);
        } else {
          console.error('Failed to load wishlist:', data.message);
        }
      } catch (err) {
        console.error('Error fetching wishlist:', err);
      } finally {
        setLoading(false);
      }
    }

    fetchWishlist();
  }, [receiver]);

  const handlePrev = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex === 0 ? wishlistItems.length - 1 : prevIndex - 1
    );
  };

  const handleNext = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex === wishlistItems.length - 1 ? 0 : prevIndex + 1
    );
  };

  const handleGiftPresented = async () => {
    try {
      const response = await api.post('/game/player/gift-presented', {
        uuid: gameUuid,
        receiver: receiver,
      });
      if (response.status === 'success') {
        setReceiverIsGifted(true);
        if (response.game_ended === true) {
          // Переходим на экран завершенной игры
          setGameEnded(true);
        }
      } else {
        console.error('Failed to mark gift as presented:', response.message);
      }
    } catch (err) {
      console.error('Error:', err);
    }
  };

  if (loading) {
    return <div className="active-game-window">Loading...</div>;
  }

  // Если игра завершена, показываем экран завершенной игры с выводом всех пар
  if (gameEnded) {
    return <FinishedGame gameUuid={gameUuid} />;
  }

  return (
    <div className="active-game-window">
      <h2>
        Ho-ho-ho! You are a Secret Santa for <br /> {receiver ? receiver : '[Unknown]'}
      </h2>
      <div className="gift-status">
        <p>
          Your gift receiving status: {userIsGifted ? 'You have received a gift!' : 'You have not received a gift yet.'}
        </p>
        <p>
          Your receiver gift status: {receiverIsGifted ? 'They have received your gift!' : 'They have not received your gift yet.'}
        </p>
      </div>

      <GiftList wishlistItems={wishlistItems} />

      <div className="active-game-buttons">
        {!receiverIsGifted && (
          <Button text={'Gift presented'} type={'submit'} onClick={handleGiftPresented} />
        )}
      </div>
    </div>
  );
};

export default ActiveGameWindow;
