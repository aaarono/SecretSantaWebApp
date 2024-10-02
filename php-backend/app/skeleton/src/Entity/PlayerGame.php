<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerGame entity class representing the "Player_Game" table in the database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PlayerGameRepository")
 * @ORM\Table(name="\"Player_Game\"")
 */
class PlayerGame
{
    /**
     * Auto-incremented primary key.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * The player (user) participating in the game.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="login", referencedColumnName="login", nullable=false)
     */
    private $player;

    /**
     * The game the player is participating in.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Game")
     * @ORM\JoinColumn(name="uuid", referencedColumnName="uuid", nullable=false)
     */
    private $game;

    /**
     * Indicates whether the player has gifted.
     *
     * @ORM\Column(type="boolean")
     */
    private $isGifted = false;

    // Getters and setters

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Player
    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(User $player): self
    {
        $this->player = $player;

        return $this;
    }

    // Game
    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    // isGifted
    public function getIsGifted(): ?bool
    {
        return $this->isGifted;
    }

    public function setIsGifted(bool $isGifted): self
    {
        $this->isGifted = $isGifted;

        return $this;
    }
}
