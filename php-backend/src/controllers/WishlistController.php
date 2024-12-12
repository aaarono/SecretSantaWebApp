<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\WishlistModel;

class WishlistController
{
    private $model;

    public function __construct()
    {
        $this->model = new WishlistModel();
    }

    private function getUserIdFromSession()
    {
        session_start();
        if (isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }
        return null;
    }

    public function getAllWishlists()
    {
        return json_encode($this->model->getAllWishlists());
    }

    public function getWishlistById($id)
    {
        $wishlist = $this->model->getWishlistById($id);
        if ($wishlist) {
            return json_encode(['status' => 'success', 'wishlist' => $wishlist]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Wishlist not found']);
        }
    }

    public function createWishlist($name, $description, $url, $login)
    {
        $success = $this->model->createWishlist($name, $description, $url, $login);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Wishlist created successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create wishlist']);
        }
    }

    public function updateWishlist($id, $name, $description, $url, $login)
    {
        $success = $this->model->updateWishlist($id, $name, $description, $url, $login);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Wishlist updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update wishlist']);
        }
    }

    public function deleteWishlist($id)
    {
        $success = $this->model->deleteWishlist($id);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Wishlist deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete wishlist']);
        }
    }

    public function getUserWishlists($userId = null)
    {
        // Если ID не передан, получаем его из сессии
        if ($userId === null) {
            $userId = $this->getUserIdFromSession();
        }

        // Проверка, есть ли ID пользователя
        if ($userId === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        // Получение желаний пользователя
        $wishlists = $this->model->getAllWishlistsByUserId($userId);
        if ($wishlists) {
            return json_encode(['status' => 'success', 'wishlists' => $wishlists]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'No wishlists found for this user']);
        }
    }
}