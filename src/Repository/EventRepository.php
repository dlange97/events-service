<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Traits\SaveRemoveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    use SaveRemoveTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Return all events belonging to the given owner, sorted by startAt ASC.
     *
     * @return Event[]
     */
    public function findAllByOwner(string $ownerId): array
    {
        return $this->findAllAccessibleByUser($ownerId);
    }

    /**
     * @return Event[]
     */
    public function findAllAccessibleByUser(string $userId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.ownerId = :userId OR e.sharedWithUserIds LIKE :sharedMatch')
            ->setParameter('userId', $userId)
            ->setParameter('sharedMatch', '%"' . $userId . '"%')
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return upcoming events (startAt >= now) for the given owner, limit 20.
     *
     * @return Event[]
     */
    public function findUpcoming(string $ownerId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.ownerId = :ownerId OR e.sharedWithUserIds LIKE :sharedMatch')
            ->andWhere('e.startAt >= :now')
            ->setParameter('ownerId', $ownerId)
            ->setParameter('sharedMatch', '%"' . $ownerId . '"%')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
