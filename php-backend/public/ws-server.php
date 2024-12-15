<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Secret\Santa\websockets\WebSocketBroadcaster;
use Secret\Santa\Controllers\SmsController;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Lobby implements MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn)
    {
        error_log("New connection opened: {$conn->resourceId}");
        $conn->send(json_encode(['type' => 'welcome', 'message' => 'Please authenticate with {type:"auth",login:"your_login"}']));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
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

                    WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
                        'type' => 'player_joined',
                        'login' => $from->userLogin,
                    ], $from);

                    $players = WebSocketBroadcaster::getInstance()->getPlayersInGame($uuid);

                    $smsController = new SmsController();
                    $messages = $smsController->getAllSms($uuid);

                    $from->send(json_encode([
                        'type' => 'joined_game',
                        'uuid' => $uuid,
                        'players' => $players,
                        'messages' => $messages,
                    ]));
                } else {
                    $from->send(json_encode(['type' => 'error', 'message' => 'Game UUID is required']));
                }
                break;

            case 'leave_game':
                $uuid = $data['uuid'] ?? null;
                if ($uuid && isset($from->userLogin)) {
                    WebSocketBroadcaster::getInstance()->leaveGame($from, $uuid);
                    error_log("Player {$from->userLogin} left game manually: $uuid");
                }
                break;
            case 'delete_game':
                $uuid = $data['uuid'] ?? null;
                if ($uuid) {
                    WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
                        'type' => 'game_deleted',
                        'uuid' => $uuid,
                        'message' => 'The game has been deleted. You have been removed from the lobby.',
                    ]);
                    error_log("Game $uuid deleted, all players removed.");
                    WebSocketBroadcaster::getInstance()->clearGame($uuid);
                }
                break;

            case 'chat_message':
                $gameUuid = $data['gameUuid'] ?? null;
                $sender = $from->userLogin ?? null;
                $content = $data['content'] ?? null;

                if ($gameUuid && $sender && $content) {
                    $smsController = new SmsController();
                    $smsController->createSms($gameUuid, $content, $sender);

                    WebSocketBroadcaster::getInstance()->broadcastToGame($gameUuid, [
                        'type' => 'chat_message',
                        'gameUuid' => $gameUuid,
                        'login' => $sender,
                        'message' => $content,
                    ]);
                } else {
                    $from->send(json_encode(['type' => 'error', 'message' => 'Invalid chat message data']));
                }
            break;
            case 'start_game':
                $uuid = $data['uuid'] ?? null;
                if (!$uuid) {
                    $from->send(json_encode(['type' => 'error', 'message' => 'Game UUID is required to start']));
                    break;
                }
            
                // Проверяем авторизацию пользователя
                if (!isset($from->userLogin)) {
                    $from->send(json_encode(['type' => 'error', 'message' => 'User not authenticated']));
                    break;
                }
            
                // Отправляем всем сообщение о том, что игра началась
                WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
                    'type' => 'game_started',
                    'uuid' => $uuid,
                    'status' => 'running'
                ]);
            
                // Отправляем инициатору подтверждение
                $from->send(json_encode(['type' => 'info', 'message' => 'Game started successfully']));
            break;
                
            default:
                error_log("Unknown message type: {$data['type']}");
                $from->send(json_encode(['type' => 'error', 'message' => 'Unknown message type']));
                break;
        }
    }


    public function onClose(ConnectionInterface $conn)
    {
        error_log("Connection closed: {$conn->resourceId}");

        // Удаляем соединение игрока
        WebSocketBroadcaster::getInstance()->removeUserConnection($conn);

        // Уведомляем игроков об уходе, если игрок был в игре
        if (isset($conn->gameUuid)) {
            WebSocketBroadcaster::getInstance()->leaveGame($conn, $conn->gameUuid);
            error_log("Player {$conn->userLogin} disconnected and left game: {$conn->gameUuid}");
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
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
