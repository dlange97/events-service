<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EntityValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validateOrFail(object $entity): void
    {
        $violations = $this->validator->validate($entity);

        if (count($violations) === 0) {
            return;
        }

        $messages = [];
        foreach ($violations as $violation) {
            $path = $violation->getPropertyPath();
            $messages[] = ($path !== '' ? "{$path}: " : '') . $violation->getMessage();
        }

        throw new \InvalidArgumentException(implode('; ', $messages));
    }
}
