<?php
namespace Secret\Santa\websockets;

use Ratchet\ConnectionInterface;

class WebSocketBroadcaster {
    private static $instance;
    private $gameClients;
    private $userConnections;

    private function __construct() {
        $this->gameClients = [];
        $this->userConnections = [];
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function associateUser(string $login, ConnectionInterface $conn) {
        if (!isset($this->userConnections[$login])) {
            $this->userConnections[$login] = new \SplObjectStorage();
        }
        $this->userConnections[$login]->attach($conn);
        $conn->userLogin = $login;
        error_log("User associated: $login");
    }

    public function removeUserConnection(ConnectionInterface $conn) {
        if (isset($conn->userLogin) && isset($this->userConnections[$conn->userLogin])) {
            $this->userConnections[$conn->userLogin]->detach($conn);
            error_log("User connection removed: {$conn->userLogin}");
        }
    }

    public function joinGame(ConnectionInterface $conn, string $gameUuid) {
        if (!isset($this->gameClients[$gameUuid])) {
            $this->gameClients[$gameUuid] = new \SplObjectStorage();
        }
        $this->gameClients[$gameUuid]->attach($conn);
        $conn->gameUuid = $gameUuid;
        error_log("Connection {$conn->resourceId} joined game: $gameUuid");
    }

    public function leaveGame(ConnectionInterface $conn, string $gameUuid) {
        if (isset($this->gameClients[$gameUuid])) {
            $this->gameClients[$gameUuid]->detach($conn);
            error_log("Connection {$conn->resourceId} left game: $gameUuid");
        }
    }

    public function joinUserToGame(string $login, string $gameUuid) {
        if (!isset($this->userConnections[$login])) {
            error_log("No connections found for user: $login");
            return;
        }
        foreach ($this->userConnections[$login] as $conn) {
            $this->joinGame($conn, $gameUuid);
        }
        error_log("User $login joined game $gameUuid");
    }

    public function broadcastToGame(string $gameUuid, array $message, ConnectionInterface $except = null) {
        if (!isset($this->gameClients[$gameUuid])) {
            error_log("No clients to broadcast to in game: $gameUuid");
            return;
        }
        $encoded = json_encode($message);
        foreach ($this->gameClients[$gameUuid] as $client) {
            if ($client !== $except) {
                $client->send($encoded);
            }
        }
        error_log("Broadcasted message to game $gameUuid: " . json_encode($message));
    }

    // Новий метод для отримання списку гравців
    public function getPlayersInGame(string $gameUuid): array {
        $players = [];
        if (!isset($this->gameClients[$gameUuid])) {
            return $players;
        }

        foreach ($this->gameClients[$gameUuid] as $conn) {
            if (isset($conn->userLogin)) {
                $players[] = $conn->userLogin;
            }
        }

        return $players;
    }
}
