<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Secret\Santa\websockets\WebSocketBroadcaster;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Lobby implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        error_log("New connection opened: {$conn->resourceId}");
        $conn->send(json_encode(['type' => 'welcome', 'message' => 'Please authenticate with {type:"auth",login:"your_login"}']));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        error_log("Message received from connection {$from->resourceId}: $msg");
    
        $data = json_decode($msg, true);
    
        if (!is_array($data)) {
            error_log("Invalid message format: $msg");
            $from->send(json_encode(['type' => 'error', 'message' => 'Invalid message format']));
            return;
        }
    
        if (!isset($from->userLogin) && ($data['type'] ?? '') !== 'auth') {
            $from->send(json_encode(['type' => 'error', 'message' => 'User not authenticated']));
            error_log("Unauthenticated message from connection {$from->resourceId}");
            return;
        }
    
        switch ($data['type'] ?? '') {
            case 'auth':
                $login = $data['login'] ?? null;
                if ($login) {
                    $from->userLogin = $login;
                    WebSocketBroadcaster::getInstance()->associateUser($login, $from);
                    error_log("User authenticated: $login");
                    $from->send(json_encode(['type' => 'auth_success', 'login' => $login]));
                } else {
                    error_log("Authentication failed: No login provided");
                    $from->send(json_encode(['type' => 'auth_error', 'message' => 'Login required']));
                }
                break;
    
                case 'join_game':
                    $uuid = $data['uuid'] ?? null;
                    if ($uuid) {
                        WebSocketBroadcaster::getInstance()->joinGame($from, $uuid);
                        error_log("User {$from->userLogin} joined game: $uuid");
                
                        // Сповіщаємо інших гравців про приєднання
                        WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
                            'type' => 'player_joined',
                            'login' => $from->userLogin,
                        ], $from);
                
                        // Отримуємо повний список гравців у лоббі
                        $players = WebSocketBroadcaster::getInstance()->getPlayersInGame($uuid);
                
                        $from->send(json_encode([
                            'type' => 'joined_game',
                            'uuid' => $uuid,
                            'players' => $players, // Повертаємо весь список гравців
                        ]));
                    } else {
                        $from->send(json_encode(['type' => 'error', 'message' => 'Game UUID is required']));
                    }
                    break;
                
    
            default:
                error_log("Unknown message type: {$data['type']}");
                $from->send(json_encode(['type' => 'error', 'message' => 'Unknown message type']));
                break;
        }
    }    
    

    public function onClose(ConnectionInterface $conn) {
        error_log("Connection closed: {$conn->resourceId}");

        WebSocketBroadcaster::getInstance()->removeUserConnection($conn);

        if (isset($conn->gameUuid)) {
            WebSocketBroadcaster::getInstance()->leaveGame($conn, $conn->gameUuid);
            error_log("Connection {$conn->resourceId} left game: {$conn->gameUuid}");
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        error_log("WebSocket error on connection {$conn->resourceId}: " . $e->getMessage());
        $conn->close();
    }
}

$port = 9090;
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Lobby()
        )
    ),
    $port
);

echo "WebSocket server running on port $port\n";
$server->run();
