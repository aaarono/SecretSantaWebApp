import React from "react";

const WishView = ({ wish }) => {
  return (
    <div>
      {wish.id === 1 ? (
        <button>{wish.text}</button> // Кнопка для добавления нового желания
      ) : (
        <div>
          <h4>{wish.text}</h4>
          <p>{wish.description}</p>
        </div>
      )}
    </div>
  );
};

export default WishView;
