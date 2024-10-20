<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class AuthService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    /**
     * Регистрация нового пользователя
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Проверка наличия обязательных полей
        $requiredFields = ['email', 'password', 'user_name', 'first_name', 'last_name', 'phone', 'gender'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Поле '{$field}' обязательно для заполнения."];
            }
        }

        // Проверка уникальности email
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['user_name' => $data['email']]);
        if ($existingUser) {
            return ['success' => false, 'message' => 'Пользователь с таким email уже существует.'];
        }

        // Создание нового пользователя
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUserName($data['user_name']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setPhone($data['phone']);
        $user->setGender($data['gender']);
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Хэширование пароля
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Валидация данных пользователя
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = $this->formatValidationErrors($errors);
            return ['success' => false, 'message' => $errorMessages];
        }

        // Сохранение пользователя в базе данных
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return ['success' => true, 'message' => 'Пользователь успешно зарегистрирован.'];
    }

    /**
     * Авторизация пользователя
     *
     * @param array $data
     * @return array
     */
    public function login(array $data): array
    {
        // Проверка наличия обязательных полей
        if (empty($data['user_name']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'user_name и пароль обязательны для входа.'];
        }

        // Поиск пользователя по email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['user_name' => $data['user_name']]);
        if (!$user) {
            return ['success' => false, 'message' => 'Неверные учетные данные.'];
        }

        // Проверка пароля
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return ['success' => false, 'message' => 'Неверные учетные данные.'];
        }

        return ['success' => true, 'message' => 'Авторизация прошла успешно.', 'user' => $user];
    }

    /**
     * Изменение пароля пользователя
     *
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function changePassword(int $userId, array $data): array
    {
        // Проверка наличия обязательных полей
        if (empty($data['old_password']) || empty($data['new_password'])) {
            return ['success' => false, 'message' => 'Старый и новый пароли обязательны для смены.'];
        }

        // Поиск пользователя по ID
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        // Проверка старого пароля
        if (!$this->passwordHasher->isPasswordValid($user, $data['old_password'])) {
            return ['success' => false, 'message' => 'Старый пароль неверен.'];
        }

        // Установка нового пароля
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['new_password']);
        $user->setPassword($hashedPassword);
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Валидация данных пользователя
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = $this->formatValidationErrors($errors);
            return ['success' => false, 'message' => $errorMessages];
        }

        $this->entityManager->flush();

        return ['success' => true, 'message' => 'Пароль успешно изменен.'];
    }

    /**
     * Форматирование ошибок валидации
     *
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }
        return $errorMessages;
    }
}
