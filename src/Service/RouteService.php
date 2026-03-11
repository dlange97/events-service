<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Route;
use App\Repository\RouteRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RouteService
{
    public function __construct(
        private readonly RouteRepository    $routeRepository,
        private readonly ValidatorInterface $validator,
    ) {}

    /**
     * Find all routes for an owner
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllByOwner(string $ownerId): array
    {
        return array_map(
            fn(Route $r) => $this->serialize($r),
            $this->routeRepository->findAllByOwner($ownerId)
        );
    }

    /**
     * Find routes for a specific event
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByEvent(string $ownerId, int $eventId): array
    {
        return array_map(
            fn(Route $r) => $this->serialize($r),
            $this->routeRepository->findByOwnerAndEvent($ownerId, $eventId)
        );
    }

    /**
     * Create a new route from GeoJSON data
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function create(string $ownerId, array $data): array
    {
        $route = new Route();
        $route->setOwnerId($ownerId);

        $this->applyData($route, $data);
        $this->validateOrFail($route);

        $this->routeRepository->save($route, true);

        return $this->serialize($route);
    }

    /**
     * Update existing route
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws \InvalidArgumentException on validation failure
     */
    public function update(Route $route, array $data): array
    {
        $this->applyData($route, $data);
        $this->validateOrFail($route);

        $this->routeRepository->save($route, true);

        return $this->serialize($route);
    }

    /**
     * Delete a route
     */
    public function delete(Route $route): void
    {
        $this->routeRepository->remove($route, true);
    }

    /**
     * Apply data to a route entity
     *
     * @param array<string, mixed> $data
     */
    private function applyData(Route $route, array $data): void
    {
        if (isset($data['name'])) {
            $route->setName($data['name']);
        }

        if (isset($data['description'])) {
            $route->setDescription($data['description']);
        }

        if (isset($data['geoJson'])) {
            $route->setGeoJson($data['geoJson']);
        }

        if (isset($data['distanceMeters'])) {
            $route->setDistanceMeters((float) $data['distanceMeters']);
        }

        if (isset($data['durationMinutes'])) {
            $route->setDurationMinutes((int) $data['durationMinutes']);
        }

        if (isset($data['waypoints'])) {
            $route->setWaypoints($data['waypoints']);
        }

        if (isset($data['eventId'])) {
            $route->setEventId($data['eventId']);
        }
    }

    /**
     * Validate a route entity
     *
     * @throws \InvalidArgumentException
     */
    private function validateOrFail(Route $route): void
    {
        $errors = $this->validator->validate($route);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = "{$error->getPropertyPath()}: {$error->getMessage()}";
            }
            throw new \InvalidArgumentException(implode('; ', $messages));
        }
    }

    /**
     * Serialize route to array
     *
     * @return array<string, mixed>
     */
    public function serialize(Route $route): array
    {
        return [
            'id'              => $route->getId(),
            'name'            => $route->getName(),
            'description'     => $route->getDescription(),
            'geoJson'         => $route->getGeoJson(),
            'distanceMeters'  => $route->getDistanceMeters(),
            'durationMinutes' => $route->getDurationMinutes(),
            'waypoints'       => $route->getWaypoints(),
            'eventId'         => $route->getEventId(),
            'createdAt'       => $route->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt'       => $route->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
