import React from "react";
import WishView from "./WishView";
import GameView from "./GameView";
import Slider from "../containers/Slider";
import { useNavigate } from 'react-router-dom';
import '../../index.css';

const MainPage = () => {
  const navigate = useNavigate();

  const wishes = [
    { id: 1, text: "Add new wish" }, // Кнопка для добавления желания
    { id: 2, text: "A big dildo", description: "Black color, price is about $10..." },
    { id: 3, text: "Another big dildo", description: "Same wish as above." },
    { id: 4, text: "Another big dildo", description: "Same wish as above." },
  ];

  const games = [
    { id: 1, status: "Active", players: "9/10", created: "14.08.2024", ends: "10d 23h 18m 15s" },
    { id: 2, status: "Ended", players: "8/8", created: "14.08.2024", ends: "25.08.2024" },
    { id: 3, status: "Active", players: "9/10", created: "14.08.2024", ends: "10d 23h 18m 15s" },
    { id: 4, status: "Active", players: "9/10", created: "14.08.2024", ends: "10d 23h 18m 15s" },
  ];

  return (
    <div className="container">
      <h2 className="header">Hello, VasyaPupkin228! Have you prepared a cookie for me?</h2>
      
      <button onClick={() => navigate('/register')} className="button">Войти в аккаунт</button>
      
      <div className="slider-container">
        <h3>Wish List</h3>
        <Slider>
          {wishes.map(wish => (
            <WishView key={wish.id} wish={wish} />
          ))}
        </Slider>
      </div>

      <div className="slider-container">
        <h3>Your Games</h3>
        <Slider>
          {games.map(game => (
            <GameView key={game.id} game={game} />
          ))}
        </Slider>
      </div>

      <button className="button">Create game</button>
      <button className="button">Connect game</button>
    </div>
  );
};

export default MainPage;
