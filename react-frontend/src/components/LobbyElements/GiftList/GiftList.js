import React from 'react';
import './GiftList.css';

const GiftList = ({ wishlistItems }) => {
  if (!wishlistItems || wishlistItems.length === 0) {
    return <p>This user has no wishlist items.</p>;
  }

  return (
    <div className="gift-list">
      {wishlistItems.map((item, index) => (
        <div className="wishlist-element-container-game" key={index}>
          <h3>{item.name}</h3>
          <p>{item.description || 'No description provided.'}</p>
          {item.url && (
            <a
              href={item.url}
              target="_blank"
              rel="noopener noreferrer"
              className="gift-link"
            >
              View Item
            </a>
          )}
        </div>
      ))}
    </div>
  );
};

export default GiftList;
