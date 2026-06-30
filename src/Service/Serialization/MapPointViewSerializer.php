<?php

declare(strict_types=1);

namespace App\Service\Serialization;

use App\Entity\MapPoint;

final class MapPointViewSerializer
{
    /** @return array<string, mixed> */
    public function serialize(MapPoint $point): array
    {
        return [
            'id' => $point->getId(),
            'name' => $point->getName(),
            'description' => $point->getDescription(),
            'lat' => $point->getLat(),
            'lon' => $point->getLon(),
            'createdAt' => $point->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt' => $point->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
