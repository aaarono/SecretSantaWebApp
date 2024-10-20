<?php

namespace App\Entity;

use App\Repository\PlayerGameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerGameRepository::class)]
class PlayerGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'playerGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_gifted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function isGifted(): ?bool
    {
        return $this->is_gifted;
    }

    public function setGifted(?bool $is_gifted): static
    {
        $this->is_gifted = $is_gifted;

        return $this;
    }
}
