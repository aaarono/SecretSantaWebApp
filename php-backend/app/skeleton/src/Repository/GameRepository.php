<?php

namespace App\Repository;

use Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * GameRepository provides custom query methods for the Game entity.
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    /**
     * Constructor to initialize the repository with the ManagerRegistry.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    // Add custom query methods below

    /**
     * Find games by status.
     *
     * @param string $status
     * @return Game[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.status = :status')
            ->setParameter('status', $status)
            ->orderBy('g.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find games that end before a certain date.
     *
     * @param \DateTimeInterface $date
     * @return Game[]
     */
    public function findGamesEndingBefore(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.endsAt <= :date')
            ->setParameter('date', $date)
            ->orderBy('g.endsAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find games created by a specific user.
     *
     * @param string $userId
     * @return Game[]
     */
    public function findByCreator(string $userId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.creator = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('g.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
