<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $user_name = null;

    #[ORM\Column(length: 30)]
    private ?string $last_name = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 10)]
    private ?string $gender = null;

    #[ORM\Column(type: Types::BLOB)]
    private $profilePhoto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\OneToMany(targetEntity: Wishlist::class, mappedBy: 'user_login')]
    private Collection $wishlists;

    /**
     * @var Collection<int, PlayerGame>
     */
    #[ORM\OneToMany(targetEntity: PlayerGame::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $playerGames;

    public function __construct()
    {
        $this->wishlists = new ArrayCollection();
        $this->playerGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function setUserName(string $user_name): static
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto($profilePhoto): static
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
        /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->user_name;
    }

    /**
     * Deprecated since Symfony 5.3, use getUserIdentifier instead.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->user_name;
    }

    /**
    * @see UserInterface
    */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // Гарантируем, что каждый пользователь имеет как минимум ROLE_USER
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setUserLogin($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getUserLogin() === $this) {
                $wishlist->setUserLogin(null);
            }
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @return Collection<int, PlayerGame>
     */
    public function getPlayerGames(): Collection
    {
        return $this->playerGames;
    }

    public function addPlayerGame(PlayerGame $playerGame): static
    {
        if (!$this->playerGames->contains($playerGame)) {
            $this->playerGames->add($playerGame);
            $playerGame->setPlayer($this);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {  }

    public function removePlayerGame(PlayerGame $playerGame): static
    {
        if ($this->playerGames->removeElement($playerGame)) {
            // set the owning side to null (unless already changed)
            if ($playerGame->getPlayer() === $this) {
                $playerGame->setPlayer(null);
            }
        }

        return $this;
    }
}
