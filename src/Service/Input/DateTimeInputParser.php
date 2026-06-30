<?php

declare(strict_types=1);

namespace App\Service\Input;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class DateTimeInputParser
{
    public function parseRequired(mixed $value, string $field): \DateTimeImmutable
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            throw new UnprocessableEntityHttpException(sprintf('Field "%s" is required.', $field));
        }

        return $this->parse($normalized, $field);
    }

    public function parseNullable(mixed $value, string $field): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        return $this->parse($normalized, $field);
    }

    private function parse(string $value, string $field): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            throw new UnprocessableEntityHttpException(sprintf('Field "%s" must be a valid datetime.', $field));
        }
    }
}
