<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity class representing the "User" table in the database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="\"User\"")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * The unique login identifier for the user.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank
     */
    private $login;

    /**
     * The email address of the user.
     *
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * The hashed password of the user.
     *
     * @ORM\Column(type="string")
     */
    private $passwordHash;

    /**
     * The first name of the user.
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * The last name of the user.
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * The phone number of the user.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * The gender of the user.
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Choice({"male", "female", "other"})
     */
    private $gender;

    /**
     * The profile photo of the user.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $profilePhoto;

    /**
     * The role of the user.
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     */
    private $role;

    /**
     * The date and time when the user was created.
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * The date and time when the user was last updated.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * The preferred language of the user.
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $language;

    /**
     * The preferred theme of the user.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $theme;

    /**
     * Constructor to initialize default values.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Implementing UserInterface and PasswordAuthenticatedUserInterface methods

    /**
     * Returns the identifier for this user (login).
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // Ensure that the roles are always an array
        return [$this->role];
    }

    /**
     * Returns the hashed password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * Not needed when using modern algorithms like bcrypt or sodium.
     *
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary sensitive data, clear it here
    }

    // Getters and setters for each property

    // Login
    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    // Email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    // Password Hash
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    // First Name
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    // Last Name
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    // Phone
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    // Gender
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    // Profile Photo
    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    // Role
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    // Created At
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Updated At
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // Language
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    // Theme
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }
}
