import React, { useEffect, useState } from 'react';
import './DeadlineTimer.css';

const DeadlineTimer = ({ endsAt }) => {
  const [timeLeft, setTimeLeft] = useState('');

  useEffect(() => {
    console.log('Received endsAt:', endsAt); // Лог для перевірки
    if (!endsAt) {
      setTimeLeft('No end time specified');
      return;
    }

    const calculateTimeLeft = () => {
      const now = new Date();
      const endTime = new Date(endsAt);

      if (isNaN(endTime.getTime())) {
        setTimeLeft('Invalid end time');
        return;
      }

      const diff = endTime - now;

      if (diff <= 0) {
        setTimeLeft('Game Over');
        return;
      }

      const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
      const minutes = Math.floor((diff / (1000 * 60)) % 60);
      const seconds = Math.floor((diff / 1000) % 60);
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));

      setTimeLeft(
        `${days > 0 ? `${days}d ` : ''}${hours}h ${minutes}m ${seconds}s`
      );
    };

    // Викликаємо розрахунок відразу
    calculateTimeLeft();

    // Запускаємо таймер, щоб оновлювати час щосекунди
    const timer = setInterval(calculateTimeLeft, 1000);

    // Очищення таймера при розмонтуванні
    return () => clearInterval(timer);
  }, [endsAt]);

  return (
    <div className="deadline-timer">
      <h4>Ends in</h4>
      <p>{timeLeft}</p>
    </div>
  );
};

export default DeadlineTimer;
