<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\Access\ResourceAccessService;
use App\Service\Input\DateTimeInputParser;
use App\Service\Input\EventLocationInputNormalizer;
use App\Service\Serialization\EventViewSerializer;
use App\Validator\EntityValidator;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EntityValidator $validator,
        private readonly EventViewSerializer $serializer,
        private readonly DateTimeInputParser $dateTimeParser,
        private readonly EventLocationInputNormalizer $locationInputNormalizer,
        private readonly ResourceAccessService $resourceAccessService,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllByOwner(string $ownerId): array
    {
        return array_map(
            fn(Event $e) => $this->serializer->serialize($e),
            $this->eventRepository->findAllAccessibleByUser($ownerId)
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findUpcoming(string $ownerId): array
    {
        return array_map(
            fn(Event $e) => $this->serializer->serialize($e),
            $this->eventRepository->findUpcoming($ownerId)
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function create(string $ownerId, array $data): array
    {
        $event = new Event();
        $event->setOwnerId($ownerId);

        if (!array_key_exists('startAt', $data)) {
            throw new UnprocessableEntityHttpException('Field "startAt" is required.');
        }

        $this->applyData($event, $data);
        $this->validator->validateOrFail($event);

        $this->eventRepository->save($event, true);

        return $this->serializer->serialize($event);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function update(Event $event, array $data): array
    {
        $this->applyData($event, $data);
        $this->validator->validateOrFail($event);

        $this->eventRepository->save($event, true);

        return $this->serializer->serialize($event);
    }

    public function delete(Event $event): void
    {
        $this->eventRepository->remove($event, true);
    }

    /**
     * @return array<string, mixed>
     */
    public function shareWithUser(Event $event, string $userId): array
    {
        $normalizedUserId = $this->resourceAccessService->normalizeShareTarget(
            $event->getOwnerId(),
            $userId,
            'Owner already has access to this event.',
        );

        $event->addSharedUserId($normalizedUserId);
        $this->eventRepository->save($event, true);

        return $this->serializer->serialize($event);
    }

    /**
     * @return array<string, mixed>
     */
    public function unshareWithUser(Event $event, string $userId): array
    {
        $event->removeSharedUserId($userId);
        $this->eventRepository->save($event, true);

        return $this->serializer->serialize($event);
    }

    public function assertOwner(Event $event, string $ownerId): void
    {
        $this->resourceAccessService->assertOwner($event->getOwnerId(), $ownerId, 'You do not own this event.');
    }

    public function assertAccessible(Event $event, string $userId): void
    {
        $this->resourceAccessService->assertAccessible(
            $event->getOwnerId(),
            $event->getSharedWithUserIds(),
            $userId,
            'You do not have access to this event.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(Event $event): array
    {
        return $this->serializer->serialize($event);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyData(Event $event, array $data): void
    {
        if (isset($data['title'])) {
            $event->setTitle((string) $data['title']);
        }

        if (array_key_exists('description', $data)) {
            $event->setDescription($data['description'] !== null ? (string) $data['description'] : null);
        }

        if (array_key_exists('startAt', $data)) {
            $event->setStartAt($this->dateTimeParser->parseRequired($data['startAt'], 'startAt'));
        }

        if (array_key_exists('endAt', $data)) {
            $event->setEndAt($this->dateTimeParser->parseNullable($data['endAt'], 'endAt'));
        }

        if (array_key_exists('location', $data)) {
            $loc = $this->locationInputNormalizer->normalize($data['location']);
            if ($loc === null) {
                $event->setLocationName(null);
                $event->setLocationLat(null);
                $event->setLocationLon(null);
            } else {
                $event->setLocationName($loc['display_name'] ?? null);
                $event->setLocationLat($loc['lat']);
                $event->setLocationLon($loc['lon']);
            }
        }
    }
}
