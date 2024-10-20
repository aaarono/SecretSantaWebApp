<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Получение данных из запроса
        $data = json_decode($request->getContent(), true);

        // Вызов метода регистрации из AuthService
        $result = $this->authService->register($data);

        return $this->json($result);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Получение данных из запроса
        $data = json_decode($request->getContent(), true);

        // Вызов метода авторизации из AuthService
        $result = $this->authService->login($data);

        // Управление сессией
        if ($result['success']) {
            $session = $request->getSession();
            $session->set('user_id', $result['user']->getId());
        }

        return $this->json($result);
    }

    #[Route('/change-password', name: 'app_change_password', methods: ['POST'])]
    public function changePassword(Request $request): JsonResponse
    {
        // Получение текущего пользователя из сессии
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->json(['success' => false, 'message' => 'User not logged in'], 401);
        }

        // Получение данных из запроса
        $data = json_decode($request->getContent(), true);

        // Вызов метода изменения пароля из AuthService
        $result = $this->authService->changePassword($userId, $data);

        return $this->json($result);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // Удаление данных пользователя из сессии
        $session = $request->getSession();
        $session->invalidate();

        return $this->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
