import React, { useState, useContext } from 'react';
import { UserContext } from '../../contexts/UserContext';

const Chat = ({ gameUuid, sendMessage, messages = [] }) => {
  const { user } = useContext(UserContext);
  const [inputMessage, setInputMessage] = useState('');

  const handleSendMessage = () => {
    if (inputMessage.trim() !== '') {
      sendMessage({
        type: 'chat_message',
        gameUuid,
        sender: user.username,
        content: inputMessage,
      });
      setInputMessage('');
    }
  };

  return (
    <div className="chat">
      <div className="chat-messages">
        {messages.map((msg, index) => (
          <div key={index}>
            <strong>{msg.login}:</strong> {msg.message}
          </div>
        ))}
      </div>
      <div className="chat-input">
        <input
          type="text"
          value={inputMessage}
          onChange={(e) => setInputMessage(e.target.value)}
          placeholder="Type a message..."
        />
        <button onClick={handleSendMessage}>Send</button>
      </div>
    </div>
  );
};

export default Chat;