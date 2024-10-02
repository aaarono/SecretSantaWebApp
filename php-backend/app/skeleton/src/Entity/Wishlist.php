<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Wishlist entity class representing the "Wishlist" table in the database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\WishlistRepository")
 * @ORM\Table(name="\"Wishlist\"")
 */
class Wishlist
{
    /**
     * Auto-incremented primary key.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Name of the wishlist item.
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * Description of the wishlist item.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * URL of the wishlist item.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private $url;

    /**
     * The user who owns this wishlist item.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="login", referencedColumnName="login", nullable=false)
     */
    private $user;

    // Getters and setters

    // ID
    public function getId(): ?int
    {
        return $this->id;
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

    // URL
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    // User
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
