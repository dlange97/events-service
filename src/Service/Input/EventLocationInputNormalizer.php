<?php

declare(strict_types=1);

namespace App\Service\Input;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class EventLocationInputNormalizer
{
    /** @return array{display_name:?string,lat:?float,lon:?float}|null */
    public function normalize(mixed $location): ?array
    {
        if ($location === null) {
            return null;
        }

        if (!is_array($location)) {
            throw new UnprocessableEntityHttpException('Field "location" must be an object or null.');
        }

        $displayName = null;
        if (array_key_exists('display_name', $location) && $location['display_name'] !== null) {
            $displayName = trim((string) $location['display_name']);
            if ($displayName === '') {
                $displayName = null;
            }
        }

        $lat = $this->normalizeFloat($location, 'lat');
        $lon = $this->normalizeFloat($location, 'lon');

        return [
            'display_name' => $displayName,
            'lat' => $lat,
            'lon' => $lon,
        ];
    }

    /** @param array<string, mixed> $location */
    private function normalizeFloat(array $location, string $field): ?float
    {
        if (!array_key_exists($field, $location) || $location[$field] === null || $location[$field] === '') {
            return null;
        }

        if (!is_numeric($location[$field])) {
            throw new UnprocessableEntityHttpException(sprintf('Field "location.%s" must be numeric.', $field));
        }

        return (float) $location[$field];
    }
}
