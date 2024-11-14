import React from 'react';
import '../../index.css';
import './WishlistElement.css';
import showMore from '../../assets/showMore.svg';
import closeCircle from '../../assets/closeCircle.svg';
import { SlClose } from "react-icons/sl";
import { SlArrowRightCircle } from "react-icons/sl";

const WishlistElement = ({ wishName, description }) => {
  return (
    <div className='wishlist-element-container'>
        <h3>{wishName}</h3>
        <p>{description}</p>
        <div className='wishlist-links'>
            <SlArrowRightCircle />
            <SlClose />
        </div>
    </div>
  );
};

export default WishlistElement;