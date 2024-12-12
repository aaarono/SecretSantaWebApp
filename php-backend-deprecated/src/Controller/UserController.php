<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/auth/update-profile', name: 'app_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request): JsonResponse
    {
        // Получение текущего пользователя из сессии
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->json(['success' => false, 'message' => 'User not logged in'], 401);
        }

        // Получение данных из запроса
        $data = json_decode($request->getContent(), true);

        // Вызов метода обновления профиля из ChangeService
        $result = $this->userService->updateProfile($userId, $data);

        return $this->json($result);
    }

    #[Route('/auth/get-profile', name: 'app_get_profile', methods: ['GET'])]
    public function getProfile(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->json(['success' => false, 'message' => 'User not logged in'], 401);
        }

        $userData = $this->userService->getUserProfile($userId);

        if (!$userData) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return $this->json(['success' => true, 'data' => $userData]);
    }
}