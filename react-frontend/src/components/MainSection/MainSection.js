import React, { useState, useEffect } from 'react';
import '../../index.css';
import './MainSection.css';
import GameElement from '../GameElement/GameElement';
import WishlistElement from '../WishlistElement/WishlistElement';
import AddNewElement from '../AddNewElement/AddNewElement';

import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import api from '../../services/api/api'; // Подключение API

const MainSection = () => {
  const [wishlist, setWishlist] = useState([]); // Состояние для списка желаний
  const [loading, setLoading] = useState(true); // Состояние загрузки

  const sliderSettings = {
    dots: false,
    infinite: false,
    speed: 500,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: false,
          dots: false,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: false,
        },
      },
    ],
  };

  const handleDeleteFromWishlist = (id) => {
    setWishlist((prevWishlist) => prevWishlist.filter((item) => item.id !== id));
  };

  const handleAddToWishlist = (newItem) => {
    setWishlist((prevWishlist) => [...prevWishlist, newItem]);
  };

  // Загрузка данных о списке желаний
  useEffect(() => {
    const fetchWishlist = async () => {
      try {
        setLoading(true);
        const response = await api.get('/user/wishlist/user');
        setWishlist(response.wishlists || []);
      } catch (error) {
        console.error('Ошибка загрузки списка желаний:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchWishlist();
  }, []);

  return (
    <div className="main-section-container">
      <div className="main-section-games">
        <h2>Active Games</h2>
        <Slider {...sliderSettings} className="main-section-game-elements">
          <div>
            <GameElement
              gameName="UPCE"
              gameStatus="Active"
              playersCount="8"
              playersMax="10"
              gameEnds="14.04.2024 | 14:44"
            />
          </div>
          <div>
            <GameElement
              gameName="UPCE"
              gameStatus="Active"
              playersCount="8"
              playersMax="10"
              gameEnds="14.04.2024 | 14:44"
            />
          </div>
          <div>
            <GameElement
              gameName="UPCE"
              gameStatus="Active"
              playersCount="8"
              playersMax="10"
              gameEnds="14.04.2024 | 14:44"
            />
          </div>
        </Slider>
      </div>
      <div className="main-section-wishlist">
        <h2>Wishlist</h2>
        {loading ? (
          <p>Loading...</p>
        ) : (
          <Slider {...sliderSettings} className="main-section-wishlist-elements">
            {wishlist.map((item) => (
              <div key={item.id}>
                <WishlistElement
                  id={item.id}
                  wishName={item.name}
                  description={item.description}
                  url={item.url}
                  onDelete={handleDeleteFromWishlist}
                />
              </div>
            ))}
            <div>
              <AddNewElement onAdd={handleAddToWishlist} />
              
            </div>
            <div>
            {wishlist.length <= 1 ? <AddNewElement onAdd={handleAddToWishlist} /> : null}
            </div>
            <div>
            {wishlist.length === 0 ? <AddNewElement onAdd={handleAddToWishlist} /> : null}
            </div>
          </Slider>
        )}
      </div>
    </div>
  );
};

export default MainSection;
