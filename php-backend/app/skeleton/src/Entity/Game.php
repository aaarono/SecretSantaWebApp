<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game entity class representing the "Game" table in the database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 * @ORM\Table(name="\"Game\"")
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $budget;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $endsAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    // Constructor
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and setters for each property

    // UUID
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    // Name
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // Description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // Budget
    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): self
    {
        $this->budget = $budget;

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

    // Created At
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Ends At
    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeInterface $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    // Status
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
