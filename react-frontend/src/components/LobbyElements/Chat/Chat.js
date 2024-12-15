import React, { useState, useContext, useCallback } from 'react';
import { UserContext } from '../../contexts/UserContext';
import useWebSocket from '../../../hooks/useWebSocket';

const Chat = ({ gameUuid }) => {
  const { user } = useContext(UserContext);
  const [messages, setMessages] = useState([]);
  const [inputMessage, setInputMessage] = useState('');

  const handleWebSocketMessage = useCallback(
    (message) => {
      if (message.type === 'chat_message' && message.gameUuid === gameUuid) {
        setMessages((prevMessages) => [...prevMessages, message]);
      }
    },
    [gameUuid],
  );

  const sendMessage = useWebSocket(handleWebSocketMessage);

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
            <strong>{msg.sender}:</strong> {msg.content}
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