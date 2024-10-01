<?php

namespace App\Service;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Entity\User;

class AuthService
{
    private $userRepository;
    private $passwordHasher;

    // public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    // {
    //     $this->userRepository = $userRepository;
    //     $this->passwordHasher = $passwordHasher;
    // }

    // public function authenticate(string $username, string $password): User
    // {
    //     $user = $this->userRepository->findOneBy(['username' => $username]);

    //     if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
    //         throw new AuthenticationException('Invalid credentials.');
    //     }

    //     return $user;
    // }
}
