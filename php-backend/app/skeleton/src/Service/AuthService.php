<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * AuthService handles user authentication, registration, and session management.
 */
class AuthService implements UserProviderInterface
{
    private $userRepository;
    private $passwordHasher;
    private $session;
    private $entityManager;

    /**
     * Constructor to inject dependencies.
     *
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * Logs in the user with the provided credentials.
     *
     * @param string $login
     * @param string $password
     * @return bool
     * @throws AuthenticationException
     */
    public function login(string $login, string $password): bool
    {
        // Find user by login
        $user = $this->userRepository->findOneBy(['login' => $login]);

        // Check if user exists and password is valid
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Create a session for the user
        $this->createSession($user);

        return true;
    }

    /**
     * Hashes the password using the password hasher.
     *
     * @param string $password
     * @return string The hashed password
     */
    public function hashPassword(string $password): string
    {
        // Create a new User object for hashing purposes
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        return $hashedPassword;
    }

    /**
     * Registers a new user with the provided registration form data.
     *
     * @param array $registerForm
     * @return User
     */
    public function register(array $registerForm): User
    {
        // Create a new User entity
        $user = new User();
        $user->setLogin($registerForm['login']);
        $user->setEmail($registerForm['email']);
        $user->setFirstName($registerForm['first_name'] ?? null);
        $user->setLastName($registerForm['last_name'] ?? null);
        $user->setPhone($registerForm['phone'] ?? null);
        $user->setGender($registerForm['gender'] ?? null);
        $user->setRole('ROLE_USER');
        $user->setLanguage($registerForm['language'] ?? null);
        $user->setTheme($registerForm['theme'] ?? null);

        // Hash and set the password
        $hashedPassword = $this->hashPassword($registerForm['password']);
        $user->setPasswordHash($hashedPassword);

        // Save the user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a session for the new user
        $this->createSession($user);

        return $user;
    }

    /**
     * Creates a session for the authenticated user.
     *
     * @param User $user
     * @return void
     */
    public function createSession(User $user): void
    {
        // Start the session if not already started
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        // Store user ID in the session
        $this->session->set('user_id', $user->getLogin());
    }

    /**
     * Closes the user's session.
     *
     * @return bool
     */
    public function closeSession(): bool
    {
        // Invalidate the current session
        $this->session->invalidate();

        return true;
    }

    /**
     * Retrieves the user associated with the current session.
     *
     * @return User|null
     */
    public function getUserBySession(): ?User
    {
        // Check if user ID is stored in the session
        $userId = $this->session->get('user_id');

        if (!$userId) {
            return null;
        }

        // Retrieve the user from the repository
        $user = $this->userRepository->find($userId);

        return $user;
    }

    /**
     * Loads the user by username (login).
     *
     * @param string $username
     * @return UserInterface
     * @throws AuthenticationException
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->userRepository->findOneBy(['login' => $username]);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        return $user;
    }

    /**
     * Loads the user by identifier (login).
     *
     * @param string $identifier
     * @return UserInterface
     * @throws AuthenticationException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['login' => $identifier]);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        return $user;
    }

    /**
     * Refreshes the user.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('Invalid user class.');
        }

        $refreshedUser = $this->userRepository->find($user->getLogin());

        if (!$refreshedUser) {
            throw new AuthenticationException('User not found.');
        }

        return $refreshedUser;
    }

    /**
     * Checks if the class supports the given user class.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
