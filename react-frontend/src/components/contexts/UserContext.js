import React, { createContext, useState, useEffect } from 'react';
import api from '../../services/api/api';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState({
    username: '',
    email: '',
  });

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await api.get('/auth/check');
        console.log('response', response);
        if (response.status === 'success') {
          setUser({
            username: response.user.username,
            email: response.user.email,
          });
        } else {
          console.error('Failed to fetch user data:', response.message);
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };

    fetchUserData();
  }, []);

  return (
    <UserContext.Provider value={{ user, setUser }}>
      {children}
    </UserContext.Provider>
  );
};
