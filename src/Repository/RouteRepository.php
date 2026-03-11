<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Route>
 */
class RouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    /**
     * Find all routes for a given owner
     *
     * @return Route[]
     */
    public function findAllByOwner(string $ownerId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find routes for a specific event
     *
     * @return Route[]
     */
    public function findByEvent(int $eventId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.eventId = :eventId')
            ->setParameter('eventId', $eventId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find routes for owner filtered by event
     *
     * @return Route[]
     */
    public function findByOwnerAndEvent(string $ownerId, int $eventId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ownerId = :ownerId')
            ->andWhere('r.eventId = :eventId')
            ->setParameter('ownerId', $ownerId)
            ->setParameter('eventId', $eventId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Route $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Route $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
