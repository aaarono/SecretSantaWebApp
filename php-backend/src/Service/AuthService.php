<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function register(array $data): array
    {
        // Проверка наличия обязательных полей
        if (empty($data['username']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        // Проверка уникальности пользователя
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
        if ($existingUser) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // Создание нового пользователя
        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return ['success' => true, 'message' => 'User registered successfully'];
    }

    public function login(array $data): array
    {
        // Проверка наличия обязательных полей
        if (empty($data['username']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        // Поиск пользователя по имени
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Проверка пароля
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }

    public function changePassword(int $userId, array $data): array
    {
        // Проверка наличия обязательных полей
        if (empty($data['old_password']) || empty($data['new_password'])) {
            return ['success' => false, 'message' => 'Old and new passwords are required'];
        }

        // Поиск пользователя по ID
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Проверка старого пароля
        if (!$this->passwordHasher->isPasswordValid($user, $data['old_password'])) {
            return ['success' => false, 'message' => 'Old password is incorrect'];
        }

        // Установка нового пароля
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['new_password'])
        );

        $this->entityManager->flush();

        return ['success' => true, 'message' => 'Password changed successfully'];
    }
}
