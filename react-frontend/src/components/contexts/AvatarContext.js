import React, { createContext, useState } from 'react';
import defaultAvatar from '../../assets/avatar.png';

// Создаём контекст
export const AvatarContext = createContext();

// Провайдер контекста
export const AvatarProvider = ({ children }) => {
  const [avatar, setAvatar] = useState(defaultAvatar);

  return (
    <AvatarContext.Provider value={{ avatar, setAvatar }}>
      {children}
    </AvatarContext.Provider>
  );
};
