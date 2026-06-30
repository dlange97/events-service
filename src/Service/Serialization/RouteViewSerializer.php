<?php

declare(strict_types=1);

namespace App\Service\Serialization;

use App\Entity\Route;

final class RouteViewSerializer
{
    /** @return array<string, mixed> */
    public function serialize(Route $route): array
    {
        return [
            'id'              => $route->getId(),
            'name'            => $route->getName(),
            'description'     => $route->getDescription(),
            'geoJson'         => $route->getGeoJson(),
            'distanceMeters'  => $route->getDistanceMeters(),
            'durationMinutes' => $route->getDurationMinutes(),
            'color'           => $route->getColor(),
            'waypoints'       => $route->getWaypoints(),
            'eventId'         => $route->getEventId(),
            'createdAt'       => $route->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt'       => $route->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
