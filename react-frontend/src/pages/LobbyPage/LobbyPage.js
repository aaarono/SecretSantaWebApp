import React from 'react';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import '../../index.css';
import './LobbyPage.css';
import GameID from '../../components/LobbyElements/GameID/GameID';
import GameBanner from '../../components/LobbyElements/GameBanner/GameBanner';
import PlayersList from '../../components/LobbyElements/PlayersList/PlayersList';
import DeadlineTimer from '../../components/LobbyElements/DeadlineTimer/DeadlineTimer';
import StartGameWindow from '../../components/LobbyElements/GameWindow/StartGameWindow';
import WaitingGameWindow from '../../components/LobbyElements/GameWindow/WaitingGameWindow';
import ActiveGameWindow from '../../components/LobbyElements/GameWindow/ActiveGameWindow';

const LobbyPage = () => {

  return (
    <>
        <div className="lobby-page-container">
                <GameID />
                <GameBanner />
                <PlayersList />
                <DeadlineTimer />
                <WaitingGameWindow /> 
                {/* <StartGameWindow />
                <ActiveGameWindow /> */}
        </div>
    </>
  );

};

export default LobbyPage;