<?php

declare(strict_types=1);

namespace App\Service\Access;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ResourceAccessService
{
    public function assertOwner(string $resourceOwnerId, string $ownerId, string $message): void
    {
        if ($resourceOwnerId !== $ownerId) {
            throw new AccessDeniedHttpException($message);
        }
    }

    /** @param list<string> $sharedWithUserIds */
    public function assertAccessible(string $resourceOwnerId, array $sharedWithUserIds, string $userId, string $message): void
    {
        if ($resourceOwnerId === $userId) {
            return;
        }

        if (in_array($userId, $sharedWithUserIds, true)) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }

    public function normalizeShareTarget(string $resourceOwnerId, string $userId, string $ownerMessage): string
    {
        $normalizedUserId = trim($userId);
        if ($normalizedUserId === '') {
            throw new \InvalidArgumentException('User ID cannot be empty.');
        }

        if ($resourceOwnerId === $normalizedUserId) {
            throw new \InvalidArgumentException($ownerMessage);
        }

        return $normalizedUserId;
    }
}