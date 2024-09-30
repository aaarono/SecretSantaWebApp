import React from "react";

const GameView = ({ game }) => {
  return (
    <div>
      <h4>Status: {game.status}</h4>
      <p>Players: {game.players}</p>
      <p>Created: {game.created}</p>
      <p>Ends: {game.ends}</p>
    </div>
  );
};

export default GameView;
