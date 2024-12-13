<?php
namespace Secret\Santa\websockets;

use Ratchet\ConnectionInterface;

class WebSocketBroadcaster {
    private static $instance;
    // Мапы:
    // gameUuid => SplObjectStorage(connections)
    private $gameClients;
    // login => SplObjectStorage(connections)
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

    // Добавляем метод, чтобы присоединять всех соединений пользователя к игре
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
            // Проверяем, чтобы сообщение не отправлялось отправителю
            if ($client !== $except) {
                $client->send($encoded);
            }
        }
        error_log("Broadcasted message to game $gameUuid: " . json_encode($message));
    }
}
