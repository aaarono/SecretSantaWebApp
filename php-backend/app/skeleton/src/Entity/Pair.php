<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pair entity class representing the "Pair" table in the database.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PairRepository")
 * @ORM\Table(name="\"Pair\"")
 */
class Pair
{
    /**
     * Composite primary key part 1: Game UUID
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Game")
     * @ORM\JoinColumn(name="game_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $game;

    /**
     * Composite primary key part 2: Gifter login
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="gifter_login", referencedColumnName="login", nullable=false)
     */
    private $gifter;

    /**
     * Receiver login
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="receiver_login", referencedColumnName="login", nullable=false)
     */
    private $receiver;

    // Getters and setters

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

    // Gifter
    public function getGifter(): ?User
    {
        return $this->gifter;
    }

    public function setGifter(User $gifter): self
    {
        $this->gifter = $gifter;

        return $this;
    }

    // Receiver
    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
