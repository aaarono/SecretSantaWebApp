<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Secret\Santa\Controllers\AuthController;
use Secret\Santa\Controllers\UserController;
use Secret\Santa\Controllers\GameController;
use Secret\Santa\Controllers\WishlistController;
use Secret\Santa\Controllers\PlayerGameController;
use Secret\Santa\Controllers\AdminController;
use Secret\Santa\Controllers\SmsController;

// Настройка CORS
$allowed_origins = ["http://localhost:3000"];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token");
    header("Access-Control-Max-Age: 86400");
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: DELETE, GET, POST, OPTIONS, PUT");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

header('Content-Type: application/json');

// Проверка сессии
function checkSession()
{
    // Установка пути для сохранения сессий
    if (defined('PHP_OS_FAMILY')) {
        switch (PHP_OS_FAMILY) {
            case 'Windows':
                $path = 'C:\\Windows\\Temp';
                break;
            case 'Linux':
            case 'Darwin':
            default:
                $path = '/tmp';
                break;
        }
    } else {
        $path = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'C:\\Windows\\Temp' : '/tmp';
    }

    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true)) {
            error_log("Не удалось создать директорию для сессий: $path");
            $path = sys_get_temp_dir();
        }
    }
    session_save_path($path);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false, // Установите true, если используете HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        http_response_code(401);
        exit();
    }
}

// Парсинг URI
$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$firstLayerRoute = $uri[0] ?? '';
$secondLayerRoute = $uri[1] ?? '';
$thirdLayerRoute = $uri[2] ?? '';

// Инициализация контроллеров
$authController = new AuthController();
$userController = new UserController();
$gameController = new GameController();
$wishlistController = new WishlistController();
$playerGameController = new PlayerGameController();
$adminController = new AdminController();
$smsController = new SmsController();

// Обработка маршрутов
switch ($firstLayerRoute) {
    case 'auth':
        switch ($secondLayerRoute) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $authController->login($input);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $authController->register($input);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'logout':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo $authController->logout();
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'change-password':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $authController->changePassword($input);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'check':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $authController->check();
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['message' => 'Auth route not found']);
                break;
        }
        break;

    case 'user':
        checkSession();
        switch ($secondLayerRoute) {
            case 'update-image':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $userId = $_POST['user_id'] ?? null;
                    $file = $_FILES['image'] ?? null;

                    if (!$file) {
                        echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
                        break;
                    }

                    echo $userController->updateUserImage($userId, $file);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'get-image':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $userId = $_GET['user_id'] ?? null;
                    echo $userController->getUserImage($userId);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'delete-image':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $userId = $_POST['user_id'] ?? null;
                    echo $userController->deleteUserImage($userId);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'get-data':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $userController->getUserData();
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'update-data':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    echo $userController->updateUserData($data);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'wishlist':
                switch ($thirdLayerRoute) {
                    case 'user':
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            $userId = $_GET['login'] ?? null;
                            echo $wishlistController->getUserWishlists($userId);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'all':
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            echo $wishlistController->getAllWishlists();
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'get':
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            $id = $_GET['id'] ?? null;
                            echo $wishlistController->getWishlistById($id);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'create':
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $input = json_decode(file_get_contents('php://input'), true);
                            error_log("Received data for 'create' endpoint: " . print_r($input, true));
                            echo $wishlistController->createWishlist($input['name'], $input['description'], $input['url'], $input['login']);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'update':
                        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                            $input = json_decode(file_get_contents('php://input'), true);
                            echo $wishlistController->updateWishlist($input['id'], $input['name'], $input['description'], $input['url'], $input['login']);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'delete':
                        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                            $input = json_decode(file_get_contents('php://input'), true);
                            echo $wishlistController->deleteWishlist($input['id']);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    default:
                        http_response_code(404);
                        echo json_encode(['message' => 'Wishlist route not found']);
                        break;
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['message' => 'User route not found']);
                break;
        }
        break;

    case 'game':
        checkSession();
        switch ($secondLayerRoute) {
            case 'start-game':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;

                    if (!$uuid) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                        break;
                    }
                    echo $gameController->startGame($uuid);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'delete-game':
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;

                    if (!$uuid) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                        break;
                    }
                    echo $gameController->deleteGame($uuid);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'end-game':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;

                    if (!$uuid) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                        break;
                    }
                    echo $gameController->endGame($uuid);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'usergames':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $login = $_GET['login'] ?? null;
                    echo $playerGameController->getUserGames($login);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;
                    $name = $input['name'] ?? null;
                    $description = $input['description'] ?? null;
                    $budget = $input['budget'] ?? null;
                    $endsAt = $input['endsAt'] ?? null;

                    if (!$name || !$endsAt || !$budget) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                        break;
                    }

                    echo $gameController->createGame($name, $description, $budget, $endsAt);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'get':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $uuid = $_GET['uuid'] ?? null;
                    if (!$uuid) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                        break;
                    }
                    echo $gameController->getGameById($uuid);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;
                    $name = $input['name'] ?? null;
                    $description = $input['description'] ?? null;
                    $budget = $input['budget'] ?? null;
                    $endsAt = $input['endsAt'] ?? null;
                    $status = $input['status'] ?? 'pending';

                    if (!$uuid || !$name || !$endsAt || !in_array($status, ['running', 'ended', 'pending'])) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing fields']);
                        break;
                    }

                    echo $gameController->updateGame($uuid, $name, $description, $budget, $endsAt, $status);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'delete':
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $uuid = $input['uuid'] ?? null;
                    if (!$uuid) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                        break;
                    }
                    echo $gameController->deleteGame($uuid);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'player':
                switch ($thirdLayerRoute) {
                    case 'add':
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $input = json_decode(file_get_contents('php://input'), true);
                            $login = $input['login'] ?? null;
                            $uuid = $input['uuid'] ?? null;

                            if (!$uuid) {
                                http_response_code(400);
                                echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                                break;
                            }

                            echo $playerGameController->addPlayerToGame($login, $uuid, null);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'remove':
                        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                            $input = json_decode(file_get_contents('php://input'), true);
                            $login = $input['login'] ?? null;
                            $uuid = $input['uuid'] ?? null;

                            if (!$uuid) {
                                http_response_code(400);
                                echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                                break;
                            }

                            echo $playerGameController->removePlayerFromGame($login, $uuid);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'creator':
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            $uuid = $_GET['uuid'] ?? null;

                            if (!$uuid) {
                                http_response_code(400);
                                echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                                break;
                            }

                            echo $gameController->getGameCreator($uuid);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    case 'list':
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            $uuid = $_GET['uuid'] ?? null;

                            if (!$uuid) {
                                http_response_code(400);
                                echo json_encode(['status' => 'error', 'message' => 'UUID is required']);
                                break;
                            }

                            echo $playerGameController->getPlayersByGameId($uuid);
                        } else {
                            http_response_code(405);
                            echo json_encode(['message' => 'Invalid request method']);
                        }
                        break;

                    default:
                        http_response_code(404);
                        echo json_encode(['message' => 'Player route not found']);
                        break;
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['message' => 'Game route not found']);
                break;
        }
        break;

    case 'sms':
        checkSession();
        switch ($secondLayerRoute) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $gameId = $input['game_id'] ?? null;
                    $message = $input['message'] ?? null;

                    if (!$gameId || !$message) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing game_id or message']);
                        break;
                    }

                    echo $smsController->createSms($gameId, $message);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'get':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $gameId = $_GET['game_id'] ?? null;
                    if (!$gameId) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing game_id']);
                        break;
                    }
                    echo $smsController->getAllSms($gameId);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'get-single':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing SMS id']);
                        break;
                    }
                    echo $smsController->getSms($id);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $id = $input['id'] ?? null;
                    $message = $input['message'] ?? null;

                    if (!$id || !$message) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing id or message']);
                        break;
                    }

                    echo $smsController->updateSms($id, $message);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'delete':
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $id = $input['id'] ?? null;

                    if (!$id) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'Missing SMS id']);
                        break;
                    }

                    echo $smsController->deleteSms($id);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['message' => 'SMS route not found']);
                break;
        }
        break;

    case 'api':
        checkSession();
        switch ($secondLayerRoute) {
            case 'users':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $adminController->getAllUsers();
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->createUser($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->updateUser($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->deleteUser($input['login']);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'games':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $adminController->getAllGames();
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->createGame($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->updateGame($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->deleteGame($input['UUID']);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'wishlists':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $adminController->getAllWishlists();
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->createWishlist($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->updateWishlist($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->deleteWishlist($input['id']);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'player_game':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $adminController->getAllPlayerGame();
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->createPlayerGame($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->updatePlayerGame($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->deletePlayerGame($input['login'], $input['UUID']);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            case 'pairs':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    echo $adminController->getAllPairs();
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->createPair($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->updatePair($input);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    echo $adminController->deletePair($input['id']);
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Invalid request method']);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['message' => 'Admin route not found']);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
        break;
}
