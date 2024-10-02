<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\GameRepository;
use Entity\User;
use Entity\Game;
use Doctrine\ORM\EntityManagerInterface;

/**
 * AdminService handles administrative actions such as managing user profiles and games.
 */
class AdminService
{
    private $userRepository;
    private $gameRepository;
    private $entityManager;

    /**
     * Constructor to inject dependencies.
     *
     * @param UserRepository $userRepository
     * @param GameRepository $gameRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserRepository $userRepository,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Sets the status of a user profile.
     *
     * @param string $profileId
     * @param string $status
     * @return bool
     */
    public function setStatus(string $profileId, string $status): bool
    {
        // Find the user by profile ID (login)
        $user = $this->userRepository->find($profileId);

        if (!$user) {
            return false;
        }

        // Set the new status (assuming 'role' is used as status)
        $user->setRole($status);

        // Persist the changes
        $this->entityManager->flush();

        return true;
    }

    /**
     * Gets the status of a user profile.
     *
     * @param string $profileId
     * @return string|null
     */
    public function getStatus(string $profileId): ?string
    {
        // Find the user by profile ID (login)
        $user = $this->userRepository->find($profileId);

        if (!$user) {
            return null;
        }

        // Return the user's status (role)
        return $user->getRole();
    }

    /**
     * Gets a user profile.
     *
     * @param string $profileId
     * @return User|null
     */
    public function getProfile(string $profileId): ?User
    {
        // Find and return the user by profile ID (login)
        return $this->userRepository->find($profileId);
    }

    /**
     * Updates a user profile with the provided data.
     *
     * @param string $profileId
     * @param array $profileData
     * @return User|null
     */
    public function updateProfile(string $profileId, array $profileData): ?User
    {
        // Find the user by profile ID (login)
        $user = $this->userRepository->find($profileId);

        if (!$user) {
            return null;
        }

        // Update user properties based on the provided data
        if (isset($profileData['email'])) {
            $user->setEmail($profileData['email']);
        }
        if (isset($profileData['first_name'])) {
            $user->setFirstName($profileData['first_name']);
        }
        if (isset($profileData['last_name'])) {
            $user->setLastName($profileData['last_name']);
        }
        if (isset($profileData['phone'])) {
            $user->setPhone($profileData['phone']);
        }
        if (isset($profileData['gender'])) {
            $user->setGender($profileData['gender']);
        }
        if (isset($profileData['language'])) {
            $user->setLanguage($profileData['language']);
        }
        if (isset($profileData['theme'])) {
            $user->setTheme($profileData['theme']);
        }
        if (isset($profileData['role'])) {
            $user->setRole($profileData['role']);
        }
        if (isset($profileData['profile_photo'])) {
            $user->setProfilePhoto($profileData['profile_photo']);
        }

        // Update the updatedAt timestamp
        $user->setUpdatedAt(new \DateTime());

        // Persist the changes
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Deletes a user profile.
     *
     * @param string $profileId
     * @return bool
     */
    public function deleteProfile(string $profileId): bool
    {
        // Find the user by profile ID (login)
        $user = $this->userRepository->find($profileId);

        if (!$user) {
            return false;
        }

        // Remove the user
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Gets a game by UUID.
     *
     * @param string $uuid
     * @return Game|null
     */
    public function getGame(string $uuid): ?Game
    {
        // Find and return the game by UUID
        return $this->gameRepository->find($uuid);
    }

    /**
     * Deletes a game by UUID.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteGame(string $uuid): bool
    {
        // Find the game by UUID
        $game = $this->gameRepository->find($uuid);

        if (!$game) {
            return false;
        }

        // Remove the game
        $this->entityManager->remove($game);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Updates a game with the provided data.
     *
     * @param string $uuid
     * @param array $gameData
     * @return Game|null
     */
    public function updateGame(string $uuid, array $gameData): ?Game
    {
        // Find the game by UUID
        $game = $this->gameRepository->find($uuid);

        if (!$game) {
            return null;
        }

        // Update game properties based on the provided data
        if (isset($gameData['name'])) {
            $game->setName($gameData['name']);
        }
        if (isset($gameData['description'])) {
            $game->setDescription($gameData['description']);
        }
        if (isset($gameData['budget'])) {
            $game->setBudget($gameData['budget']);
        }
        if (isset($gameData['theme'])) {
            $game->setTheme($gameData['theme']);
        }
        if (isset($gameData['status'])) {
            $game->setStatus($gameData['status']);
        }
        if (isset($gameData['ends_at'])) {
            $game->setEndsAt(new \DateTime($gameData['ends_at']));
        }


        // Persist the changes
        $this->entityManager->flush();

        return $game;
    }
}
