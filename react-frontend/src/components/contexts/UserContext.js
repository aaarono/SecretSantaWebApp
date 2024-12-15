import React, { createContext, useState, useEffect } from 'react';
import api from '../../services/api/api';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
    const [user, setUser] = useState({
        username: '',
        email: '',
        sessionId: ''
    });

    useEffect(() => {
        fetchUserData();
    }, []);

    const fetchUserData = async () => {
        try {
            const response = await api.get('/auth/check');
            if (response.status === 'success') {
                setUser({
                    username: response.user.username,
                    email: response.user.email,
                    sessionId: response.sessionId, // Сохраняем sessionId
                });
            } else {
                console.error('Failed to fetch user data:', response.message);
            }
        } catch (error) {
            console.error('Error fetching user data:', error);
        }
    };

    const updateUser = async (updatedData) => {
      try {
          const response = await api.post('/user/update-data', updatedData);
          if (response.status === 'success') {
              setUser(prevUser => ({
                  ...prevUser,
                  ...updatedData
              }));
              alert('User data updated successfully');
          } else {
              alert(response.message || 'Failed to update user data');
          }
      } catch (error) {
          console.error('Error updating user data:', error);
          alert('An error occurred while updating user data');
      }
  };

    const clearUser = () => setUser(null);

    return (
        <UserContext.Provider value={{ user, setUser, updateUser, fetchUserData, clearUser }}>
            {children}
        </UserContext.Provider>
    );
};
