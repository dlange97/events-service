<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Return all events for an owner, serialised as plain arrays.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllByOwner(string $ownerId): array
    {
        return array_map(
            fn(Event $e) => $this->serialize($e),
            $this->eventRepository->findAllByOwner($ownerId)
        );
    }

    /**
     * Return upcoming events for an owner, serialised as plain arrays.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findUpcoming(string $ownerId): array
    {
        return array_map(
            fn(Event $e) => $this->serialize($e),
            $this->eventRepository->findUpcoming($ownerId)
        );
    }

    /**
     * Create a new event from raw payload data.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function create(string $ownerId, array $data): array
    {
        $event = new Event();
        $event->setOwnerId($ownerId);

        $this->applyData($event, $data);
        $this->validateOrFail($event);

        $this->eventRepository->save($event, true);

        return $this->serialize($event);
    }

    /**
     * Update an existing event.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function update(Event $event, array $data): array
    {
        $this->applyData($event, $data);
        $this->validateOrFail($event);

        $this->eventRepository->save($event, true);

        return $this->serialize($event);
    }

    /**
     * Delete an event.
     */
    public function delete(Event $event): void
    {
        $this->eventRepository->remove($event, true);
    }

    /**
     * Serialise an Event entity to an array safe for JSON output.
     *
     * @return array<string, mixed>
     */
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
            'createdAt'   => $event->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt'   => $event->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }

    // ── Private helpers ───────────────────────────────────────

    /**
     * Apply payload fields to an Event entity.
     *
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

        if (!empty($data['startAt'])) {
            $event->setStartAt(new \DateTimeImmutable((string) $data['startAt']));
        }

        if (array_key_exists('endAt', $data)) {
            $event->setEndAt(!empty($data['endAt']) ? new \DateTimeImmutable((string) $data['endAt']) : null);
        }

        // Location block: { display_name, lat, lon }
        if (array_key_exists('location', $data)) {
            $loc = $data['location'];
            if ($loc === null) {
                $event->setLocationName(null);
                $event->setLocationLat(null);
                $event->setLocationLon(null);
            } else {
                $event->setLocationName($loc['display_name'] ?? null);
                $event->setLocationLat(isset($loc['lat']) ? (float) $loc['lat'] : null);
                $event->setLocationLon(isset($loc['lon']) ? (float) $loc['lon'] : null);
            }
        }
    }

    /**
     * Run Symfony validation; throw InvalidArgumentException with JSON-friendly messages on failure.
     *
     * @throws \InvalidArgumentException
     */
    private function validateOrFail(Event $event): void
    {
        $violations = $this->validator->validate($event);
        if (count($violations) === 0) {
            return;
        }

        $messages = [];
        foreach ($violations as $v) {
            $messages[] = $v->getMessage();
        }

        throw new \InvalidArgumentException(implode(' ', $messages));
    }
}
