<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Обновление данных профиля пользователя
     *
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function updateProfile(int $userId, array $data): array
    {
        // Поиск пользователя по ID
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        // Проверка и обновление данных, если они отличаются
        $hasChanges = false;

        if (isset($data['email']) && $data['email'] !== $user->getEmail()) {
            $user->setEmail($data['email']);
            $hasChanges = true;
        }

        if (isset($data['user_name']) && $data['user_name'] !== $user->getUserName()) {
            $user->setUserName($data['user_name']);
            $hasChanges = true;
        }

        if (isset($data['phone']) && $data['phone'] !== $user->getPhone()) {
            $user->setPhone($data['phone']);
            $hasChanges = true;
        }

        if (isset($data['first_name']) && $data['first_name'] !== $user->getFirstName()) {
            $user->setFirstName($data['first_name']);
            $hasChanges = true;
        }

        if (isset($data['last_name']) && $data['last_name'] !== $user->getLastName()) {
            $user->setLastName($data['last_name']);
            $hasChanges = true;
        }

        if (isset($data['gender']) && $data['gender'] !== $user->getGender()) {
            $user->setGender($data['gender']);
            $hasChanges = true;
        }

        // Если изменений нет, возвращаем соответствующее сообщение
        if (!$hasChanges) {
            return ['success' => true, 'message' => 'Нет изменений для обновления.'];
        }

        // Валидация данных пользователя
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = $this->formatValidationErrors($errors);
            return ['success' => false, 'message' => $errorMessages];
        }

        // Обновление времени изменения и сохранение изменений в базе данных
        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return ['success' => true, 'message' => 'Профиль успешно обновлен.'];
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

    public function getUserProfile(int $userId): ?array
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return null;
        }

        // Возвращаем данные пользователя
        return [
            'email' => $user->getEmail(),
            'user_name' => $user->getUserName(),
            'phone' => $user->getPhone(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'gender' => $user->getGender(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ];
    }
}