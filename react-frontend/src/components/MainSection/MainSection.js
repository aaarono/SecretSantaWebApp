// MainSection.js
import React from 'react';
import '../../index.css';
import './MainSection.css';
import GameElement from '../GameElement/GameElement';
import WishlistElement from '../WishlistElement/WishlistElement';
import AddNewElement from '../AddNewElement/AddNewElement';

import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';

const MainSection = () => {
  const sliderSettings = {
    dots: false, // Показывать индикаторы
    infinite: false, // Бесконечная прокрутка отключена
    speed: 500, // Скорость анимации в мс
    slidesToShow: 3, // Количество видимых слайдов
    slidesToScroll: 1, // Количество слайдов при прокрутке
    responsive: [
      {
        breakpoint: 1024, // Для экранов меньше 1024px
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: false,
          dots: false,
        },
      },
      {
        breakpoint: 600, // Для экранов меньше 600px
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: false,
        },
      },
    ],
  };

  return (
    <div className='main-section-container'>
      <div className='main-section-games'>
        <h2>Active Games</h2>
        <Slider {...sliderSettings} className='main-section-game-elements'>
          <div>
            <GameElement
              gameName='UPCE'
              gameStatus='Active'
              playersCount='8'
              playersMax='10'
              gameEnds='14.04.2024 | 14:44'
            />
          </div>
          <div>
            <GameElement
              gameName='UPCE'
              gameStatus='Active'
              playersCount='8'
              playersMax='10'
              gameEnds='14.04.2024 | 14:44'
            />
          </div>
          <div>
            <GameElement
              gameName='UPCE'
              gameStatus='Active'
              playersCount='8'
              playersMax='10'
              gameEnds='14.04.2024 | 14:44'
            />
          </div>
          <div>
            <AddNewElement />
          </div>
        </Slider>
      </div>
      <div className='main-section-wishlist'>
        <h2>Wishlist</h2>
        <Slider {...sliderSettings} className='main-section-wishlist-elements'>
          <div>
            <WishlistElement
              wishName='PlayStation 5'
              description='Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.'
            />
          </div>
          <div>
            <WishlistElement
              wishName='PlayStation 5'
              description='Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.'
            />
          </div>
          <div>
            <AddNewElement />
          </div>
        </Slider>
      </div>
    </div>
  );
};

export default MainSection;
