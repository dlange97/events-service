<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MapPoint;
use App\Traits\SaveRemoveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MapPoint>
 */
class MapPointRepository extends ServiceEntityRepository
{
    use SaveRemoveTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapPoint::class);
    }

    /**
     * @return MapPoint[]
     */
    public function findAllByOwner(string $ownerId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
