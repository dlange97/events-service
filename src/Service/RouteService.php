<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Route;
use App\Repository\RouteRepository;
use App\Service\Access\ResourceAccessService;
use App\Service\Serialization\RouteViewSerializer;
use App\Validator\EntityValidator;

final class RouteService
{
    public function __construct(
        private readonly RouteRepository $routeRepository,
        private readonly EntityValidator $validator,
        private readonly RouteViewSerializer $serializer,
        private readonly ResourceAccessService $resourceAccessService,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllByOwner(string $ownerId): array
    {
        return array_map(
            fn(Route $r) => $this->serializer->serialize($r),
            $this->routeRepository->findAllByOwner($ownerId)
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByEvent(string $ownerId, int $eventId): array
    {
        return array_map(
            fn(Route $r) => $this->serializer->serialize($r),
            $this->routeRepository->findByOwnerAndEvent($ownerId, $eventId)
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function create(string $ownerId, array $data): array
    {
        $route = new Route();
        $route->setOwnerId($ownerId);

        $this->applyData($route, $data);
        $this->validator->validateOrFail($route);

        $this->routeRepository->save($route, true);

        return $this->serializer->serialize($route);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function update(Route $route, array $data): array
    {
        $this->applyData($route, $data);
        $this->validator->validateOrFail($route);

        $this->routeRepository->save($route, true);

        return $this->serializer->serialize($route);
    }

    public function delete(Route $route): void
    {
        $this->routeRepository->remove($route, true);
    }

    public function assertOwner(Route $route, string $ownerId): void
    {
        $this->resourceAccessService->assertOwner($route->getOwnerId(), $ownerId, 'You do not own this route.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyData(Route $route, array $data): void
    {
        if (array_key_exists('name', $data) && $data['name'] !== null) {
            $route->setName((string) $data['name']);
        }

        if (array_key_exists('description', $data)) {
            $route->setDescription($data['description'] !== null ? (string) $data['description'] : null);
        }

        if (array_key_exists('geoJson', $data) && is_array($data['geoJson'])) {
            $route->setGeoJson($data['geoJson']);
        }

        if (array_key_exists('distanceMeters', $data)) {
            $route->setDistanceMeters($data['distanceMeters'] !== null ? (float) $data['distanceMeters'] : null);
        }

        if (array_key_exists('durationMinutes', $data)) {
            $route->setDurationMinutes($data['durationMinutes'] !== null ? (int) $data['durationMinutes'] : null);
        }

        if (array_key_exists('color', $data)) {
            $route->setColor((string) ($data['color'] ?? ''));
        }

        if (array_key_exists('waypoints', $data) && is_array($data['waypoints'])) {
            $route->setWaypoints($data['waypoints']);
        }

        if (array_key_exists('eventId', $data)) {
            $route->setEventId($data['eventId'] !== null ? (int) $data['eventId'] : null);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(Route $route): array
    {
        return $this->serializer->serialize($route);
    }
}
