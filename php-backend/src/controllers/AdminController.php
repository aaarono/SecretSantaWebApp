<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\UserModel;
use Secret\Santa\Models\GameModel;
use Secret\Santa\Models\WishlistModel;
use Secret\Santa\Models\PlayerGameModel;
use Secret\Santa\Models\PairModel;
use Secret\Santa\Models\SmsModel;

class AdminController
{
    private $userModel;
    private $gameModel;
    private $wishlistModel;
    private $playerGameModel;
    private $pairModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->gameModel = new GameModel();
        $this->wishlistModel = new WishlistModel();
        $this->playerGameModel = new PlayerGameModel();
        $this->pairModel = new PairModel();
        $this->smsModel = new SmsModel();

    }

    //SMS CRUD operations
    public function getAllSms()
    {
        return json_encode($this->smsModel->getAllSms());
    }

    public function createSms($data)
    {
        $sanitizedData = [
            'game_id' => filter_var($data['game_id'] ?? '', FILTER_SANITIZE_STRING),
            'login'   => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING),
            'message' => filter_var($data['message'] ?? '', FILTER_SANITIZE_STRING),
        ];

        if (!$sanitizedData['game_id'] || !$sanitizedData['login'] || !$sanitizedData['message']) {
            return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        }

        $createSuccess = $this->smsModel->createSms(
            $sanitizedData['game_id'],
            $sanitizedData['login'],
            $sanitizedData['message']
        );

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'SMS created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create SMS']);
        }
    }

    public function updateSms($data)
    {
        $sanitizedData = [
            'id'      => $data['id'] ?? null,
            'message' => filter_var($data['message'] ?? '', FILTER_SANITIZE_STRING),
        ];

        if (!$sanitizedData['id'] || !$sanitizedData['message']) {
            return json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        }

        $updateSuccess = $this->smsModel->updateSms(
            $sanitizedData['id'],
            $sanitizedData['message']
        );

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'SMS updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update SMS']);
        }
    }

    public function deleteSms($id)
    {
        $deleteSuccess = $this->smsModel->deleteSms($id);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'SMS deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete SMS']);
        }
    }

    // User CRUD operations
    public function getAllUsers()
    {
        return json_encode($this->userModel->getAllUsers());
    }

    public function createUser($data)
    {
        $sanitizedData = [
            'login'        => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING),
            'email'        => filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'password'     => password_hash($data['password'] ?? '', PASSWORD_BCRYPT),
            'firstName'    => filter_var($data['firstName'] ?? '', FILTER_SANITIZE_STRING),
            'lastName'     => filter_var($data['lastName'] ?? '', FILTER_SANITIZE_STRING),
            'phone'        => filter_var($data['phone'] ?? '', FILTER_SANITIZE_STRING),
            'gender'       => filter_var($data['gender'] ?? '', FILTER_SANITIZE_STRING),
            'role'         => filter_var($data['role'] ?? 'regular', FILTER_SANITIZE_STRING),
            'language'     => filter_var($data['language'] ?? '', FILTER_SANITIZE_STRING),
            'profile_photo'=> $data['profile_photo'] ?? null
        ];

        if (!$sanitizedData['email']) {
            return json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        }

        $createSuccess = $this->userModel->createUser($sanitizedData);

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'User created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create user']);
        }
    }

    public function updateUser($data)
    {
        $sanitizedData = [
            'login'        => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING),
            'email'        => filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'firstName'    => filter_var($data['firstname'] ?? '', FILTER_SANITIZE_STRING),
            'lastName'     => filter_var($data['lastname'] ?? '', FILTER_SANITIZE_STRING),
            'phone'        => filter_var($data['phone'] ?? '', FILTER_SANITIZE_NUMBER_INT),
            'gender'       => filter_var($data['gender'] ?? '', FILTER_SANITIZE_STRING),
            'role'         => filter_var($data['role'] ?? 'regular', FILTER_SANITIZE_STRING),
            'language'     => filter_var($data['language'] ?? '', FILTER_SANITIZE_STRING),
            'profile_photo'=> $data['profile_photo'] ?? null
        ];

        if (!$sanitizedData['email']) {
            return json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        }

        error_log($data['firstName']);

        $updateSuccess = $this->userModel->updateUserData($sanitizedData['login'], $sanitizedData);

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'User updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update user']);
        }
    }

    public function deleteUser($login)
    {
        $deleteSuccess = $this->userModel->deleteUser($login);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
        }
    }

    // Game CRUD operations
    public function getAllGames()
    {
        return json_encode($this->gameModel->getAllGames());
    }

    public function createGame($data)
    {
        $sanitizedData = [
            'UUID'         => $data['UUID'] ?? null,
            'Name'         => filter_var($data['Name'] ?? '', FILTER_SANITIZE_STRING),
            'Description'  => filter_var($data['Description'] ?? '', FILTER_SANITIZE_STRING),
            'Budget'       => filter_var($data['Budget'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'EndsAt'       => filter_var($data['EndsAt'] ?? '', FILTER_SANITIZE_STRING),
            'Status'       => filter_var($data['Status'] ?? 'pending', FILTER_SANITIZE_STRING),
            'creator_login'=> filter_var($data['creator_login'] ?? '', FILTER_SANITIZE_STRING)
        ];

        $createSuccess = $this->gameModel->createGame(
            $sanitizedData['UUID'],
            $sanitizedData['Name'],
            $sanitizedData['Description'],
            $sanitizedData['Budget'],
            $sanitizedData['EndsAt'],
            $sanitizedData['creator_login'],
            $sanitizedData['Status']
        );

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Game created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create game']);
        }
    }

    public function updateGame($data)
    {
        $sanitizedData = [
            'uuid'         => $data['uuid'] ?? null,
            'name'         => filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING),
            'description'  => filter_var($data['description'] ?? '', FILTER_SANITIZE_STRING),
            'budget'       => filter_var($data['budget'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'endsat'       => filter_var($data['endsat'] ?? '', FILTER_SANITIZE_STRING),
            'status'       => filter_var($data['status'] ?? 'pending', FILTER_SANITIZE_STRING)
        ];

        $updateSuccess = $this->gameModel->updateGame(
            $sanitizedData['uuid'],
            $sanitizedData['name'],
            $sanitizedData['description'],
            $sanitizedData['budget'],
            $sanitizedData['endsat'],
            $sanitizedData['status']
        );

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Game updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update game']);
        }
    }

    public function deleteGame($UUID)
    {
        $deleteSuccess = $this->gameModel->deleteGame($UUID);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Game deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete game']);
        }
    }

    // Wishlist CRUD operations
    public function getAllWishlists()
    {
        return json_encode($this->wishlistModel->getAllWishlists());
    }

    public function createWishlist($data)
    {
        $sanitizedData = [
            'name'        => filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING),
            'description' => filter_var($data['description'] ?? '', FILTER_SANITIZE_STRING),
            'url'         => filter_var($data['url'] ?? '', FILTER_SANITIZE_URL),
            'login'       => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING)
        ];

        $createSuccess = $this->wishlistModel->createWishlist($sanitizedData['name'], $sanitizedData['description'], $sanitizedData['url'], $sanitizedData['login']);

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Wishlist created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create wishlist']);
        }
    }

    public function updateWishlist($data)
    {
        $sanitizedData = [
            'id'          => $data['id'] ?? null,
            'name'        => filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING),
            'description' => filter_var($data['description'] ?? '', FILTER_SANITIZE_STRING),
            'url'         => filter_var($data['url'] ?? '', FILTER_SANITIZE_URL),
            'login'       => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING)
        ];

        $updateSuccess = $this->wishlistModel->updateWishlist($sanitizedData['id'], $sanitizedData['name'], $sanitizedData['description'], $sanitizedData['url'], $sanitizedData['login']);

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Wishlist updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update wishlist']);
        }
    }

    public function deleteWishlist($id)
{
    $deleteSuccess = $this->wishlistModel->deleteWishlist($id);

    if ($deleteSuccess) {
        error_log("Wishlist with ID {$id} deleted successfully.");
        return json_encode(['status' => 'success', 'message' => 'Wishlist deleted successfully']);
    } else {
        error_log("Failed to delete wishlist with ID {$id}.");
        return json_encode(['status' => 'error', 'message' => 'Failed to delete wishlist']);
    }
}


    // Player_Game CRUD operations
    public function getAllPlayerGame()
    {
        return json_encode($this->playerGameModel->getAllPlayerGame());
    }

    public function createPlayerGame($data)
    {
        $sanitizedData = [
            'login'   => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING),
            'UUID'    => $data['UUID'] ?? null,
            'is_gifted' => filter_var($data['is_gifted'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ];

        $createSuccess = $this->playerGameModel->createPlayerGame($sanitizedData);

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Player_Game created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create Player_Game']);
        }
    }

    public function updatePlayerGame($data)
    {
        $sanitizedData = [
            'login'   => filter_var($data['login'] ?? '', FILTER_SANITIZE_STRING),
            'uuid'    => $data['uuid'] ?? null,
            'is_gifted' => filter_var($data['is_gifted'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ];

        $updateSuccess = $this->playerGameModel->updatePlayerGame($sanitizedData['login'], $sanitizedData['uuid'], $sanitizedData['is_gifted']);

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Player_Game updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update Player_Game']);
        }
    }

    public function deletePlayerGame($login, $UUID)
    {
        $deleteSuccess = $this->playerGameModel->deletePlayerGame($login, $UUID);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Player_Game deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete Player_Game']);
        }
    }

    // Pair CRUD operations
    public function getAllPairs()
    {
        return json_encode($this->pairModel->getAllPairs());
    }

    public function createPair($data)
    {
        $sanitizedData = [
            'game_id'     => $data['game_id'] ?? null,
            'gifter_id'   => filter_var($data['gifter_id'] ?? '', FILTER_SANITIZE_STRING),
            'receiver_id' => filter_var($data['receiver_id'] ?? '', FILTER_SANITIZE_STRING)
        ];

        $createSuccess = $this->pairModel->createPair($sanitizedData);

        if ($createSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Pair created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create pair']);
        }
    }

    public function updatePair($data)
    {
        $sanitizedData = [
            'id'          => $data['id'] ?? null,
            'game_id'     => $data['game_id'] ?? null,
            'gifter_id'   => filter_var($data['gifter_id'] ?? '', FILTER_SANITIZE_STRING),
            'receiver_id' => filter_var($data['receiver_id'] ?? '', FILTER_SANITIZE_STRING)
        ];

        $updateSuccess = $this->pairModel->updatePair($sanitizedData);

        if ($updateSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Pair updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update pair']);
        }
    }

    public function deletePair($id)
    {
        $deleteSuccess = $this->pairModel->deletePair($id);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Pair deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete pair']);
        }
    }
}

?>