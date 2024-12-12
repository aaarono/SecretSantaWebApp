<?php

namespace App\Service;

use App\Entity\Wishlist;
use App\Entity\User;
use App\Repository\WishlistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WishListService
{
    private EntityManagerInterface $entityManager;
    private WishlistRepository $wishlistRepository;
    private UserRepository $userRepository;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        WishlistRepository $wishlistRepository,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }
    
    // В методе addWish добавьте:
    public function addWish(SessionInterface $session, array $wish): bool
    {
        $user = $this->getUserFromSession($session);
        if (!$user) {
            return false;
        }
    
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'description' => [new Assert\Optional([new Assert\Length(['max' => 1000])])],
            'url' => [new Assert\Optional([new Assert\Url()])]
        ]);
    
        $errors = $this->validator->validate($wish, $constraints);
        if (count($errors) > 0) {
            // Можно логировать ошибки или обработать их иначе
            return false;
        }
    }
    /**
     * Обновляет существующее желание.
     *
     * @param SessionInterface $session
     * @param int $wishId
     * @param array $wish
     * @return bool
     */
    public function updateWish(SessionInterface $session, int $wishId, array $wish): bool
    {
        $user = $this->getUserFromSession($session);
        if (!$user) {
            return false;
        }

        $existingWish = $this->wishlistRepository->find($wishId);
        if (!$existingWish || $existingWish->getUserLogin()->getId() !== $user->getId()) {
            return false;
        }

        $existingWish->setName($wish['name'] ?? $existingWish->getName());
        $existingWish->setDescription($wish['description'] ?? $existingWish->getDescription());
        $existingWish->setUrl($wish['url'] ?? $existingWish->getUrl());

        $this->entityManager->flush();

        return true;
    }

    /**
     * Получает конкретное желание пользователя.
     *
     * @param SessionInterface $session
     * @param int $wishId
     * @return Wishlist|null
     */
    public function getWish(SessionInterface $session, int $wishId): ?Wishlist
    {
        $user = $this->getUserFromSession($session);
        if (!$user) {
            return null;
        }

        $wish = $this->wishlistRepository->find($wishId);
        if ($wish && $wish->getUserLogin()->getId() === $user->getId()) {
            return $wish;
        }

        return null;
    }

    /**
     * Удаляет желание пользователя.
     *
     * @param SessionInterface $session
     * @param int $wishId
     * @return bool
     */
    public function deleteWish(SessionInterface $session, int $wishId): bool
    {
        $user = $this->getUserFromSession($session);
        if (!$user) {
            return false;
        }

        $wish = $this->wishlistRepository->find($wishId);
        if ($wish && $wish->getUserLogin()->getId() === $user->getId()) {
            $this->entityManager->remove($wish);
            $this->entityManager->flush();
            return true;
        }

        return false;
    }

    /**
     * Получает все желания пользователя.
     *
     * @param SessionInterface $session
     * @return array
     */
    public function getWishes(SessionInterface $session): array
    {
        $user = $this->getUserFromSession($session);
        if (!$user) {
            return [];
        }

        return $this->wishlistRepository->findBy(['user_login' => $user]);
    }

    /**
     * Вспомогательный метод для получения пользователя из сессии.
     *
     * @param SessionInterface $session
     * @return User|null
     */
    private function getUserFromSession(SessionInterface $session): ?User
    {
        $userId = $session->get('user_id');
        if (!$userId) {
            return null;
        }

        return $this->userRepository->find($userId);
    }
}
