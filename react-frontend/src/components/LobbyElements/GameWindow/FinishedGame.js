import React, { useEffect, useState } from 'react';
import api from '../../../services/api/api';

const FinishedGame = ({ gameUuid }) => {
  const [pairs, setPairs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchPairs() {
      try {
        const data = await api.get(`/game/pairs?uuid=${gameUuid}`);
        if (data.status === 'success') {
          setPairs(data.pairs || []);
        } else {
          console.error('Failed to load pairs:', data.message);
        }
      } catch (err) {
        console.error('Error fetching pairs:', err);
      } finally {
        setLoading(false);
      }
    }

    fetchPairs();
  }, [gameUuid]);

  if (loading) {
    return <div className="finished-game-window">Loading pairs...</div>;
  }

  return (
    <div className="finished-game-window">
      <h2>The game has ended!</h2>
      <p>All gifts have been exchanged. Ho-ho-ho!</p>
      <h3>Pairs:</h3>
      <ul>
        {pairs.map((pair, index) => (
          <li key={index}>
            <strong>{pair.gifter_id}</strong> → {pair.receiver_id}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default FinishedGame;
