import React, { useState } from "react";
import '../../index.css';

const Slider = ({ children }) => {
  const [currentIndex, setCurrentIndex] = useState(0);

  const itemsPerPage = 3; // По 3 элемента на слайде
  const totalItems = children.length;
  
  const prevSlide = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex === 0 ? totalItems - itemsPerPage : prevIndex - 1
    );
  };

  const nextSlide = () => {
    setCurrentIndex((prevIndex) =>
      prevIndex >= totalItems - itemsPerPage ? 0 : prevIndex + 1
    );
  };

  return (
    <div className="slider">
      <button onClick={prevSlide} className="slider-button">{"<"}</button>
      <div className="slider">
        {children.slice(currentIndex, currentIndex + itemsPerPage).map((child, index) => (
          <div key={index} className="slider-item">
            {child}
          </div>
        ))}
      </div>
      <button onClick={nextSlide} className="slider-button">{">"}</button>
    </div>
  );
};

export default Slider;
