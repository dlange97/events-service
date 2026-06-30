<?php

declare(strict_types=1);

namespace App\Service\Serialization;

use App\Entity\Event;

final class EventViewSerializer
{
    /** @return array<string, mixed> */
    public function serialize(Event $event): array
    {
        $location = null;
        if ($event->getLocationName() !== null) {
            $location = [
                'display_name' => $event->getLocationName(),
                'lat'          => $event->getLocationLat(),
                'lon'          => $event->getLocationLon(),
            ];
        }

        return [
            'id'          => $event->getId(),
            'title'       => $event->getTitle(),
            'description' => $event->getDescription(),
            'startAt'     => $event->getStartAt()?->format(\DateTimeInterface::ATOM),
            'endAt'       => $event->getEndAt()?->format(\DateTimeInterface::ATOM),
            'location'    => $location,
            'ownerId'     => $event->getOwnerId(),
            'sharedWithUserIds' => $event->getSharedWithUserIds(),
            'createdAt'   => $event->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt'   => $event->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
