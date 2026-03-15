<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MapPoint;
use App\Repository\MapPointRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MapPointService
{
    public function __construct(
        private readonly MapPointRepository $mapPointRepository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllByOwner(string $ownerId): array
    {
        return array_map(
            fn(MapPoint $point) => $this->serialize($point),
            $this->mapPointRepository->findAllByOwner($ownerId),
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(string $ownerId, array $data): array
    {
        $point = new MapPoint();
        $point->setOwnerId($ownerId);

        $this->applyData($point, $data);
        $this->validateOrFail($point);

        $this->mapPointRepository->save($point, true);

        return $this->serialize($point);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(MapPoint $point, array $data): array
    {
        $this->applyData($point, $data);
        $this->validateOrFail($point);

        $this->mapPointRepository->save($point, true);

        return $this->serialize($point);
    }

    public function delete(MapPoint $point): void
    {
        $this->mapPointRepository->remove($point, true);
    }

    public function assertOwner(MapPoint $point, string $ownerId): void
    {
        if ($point->getOwnerId() !== $ownerId) {
            throw new AccessDeniedHttpException('You do not own this map point.');
        }
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param array<string, mixed> $data
     */
    private function applyData(MapPoint $point, array $data): void
    {
        if (isset($data['name'])) {
            $point->setName((string) $data['name']);
        }

        if (array_key_exists('description', $data)) {
            $point->setDescription($data['description'] !== null ? (string) $data['description'] : null);
        }

        if (isset($data['lat'])) {
            $point->setLat((float) $data['lat']);
        }

        if (isset($data['lon'])) {
            $point->setLon((float) $data['lon']);
        }
    }

    private function validateOrFail(MapPoint $point): void
    {
        $errors = $this->validator->validate($point);
        if (count($errors) === 0) {
            return;
        }

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }

        throw new \InvalidArgumentException(implode('; ', $messages));
    }
}
