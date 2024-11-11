import React from 'react';
import '../../index.css';
import './WishlistElement.css';
import showMore from '../../assets/showMore.svg';
import closeCircle from '../../assets/closeCircle.svg';

const WishlistElement = ({ wishName, description }) => {
  return (
    <div className='wishlist-element-container'>
        <h3>{wishName}</h3>
        <p>{description}</p>
        <div className='wishlist-links'>
            <a href='#'><img src={showMore} alt='Edit'/></a>
            <a href='#'><img src={closeCircle} alt='Delete'/></a>
        </div>
    </div>
  );
};

export default WishlistElement;