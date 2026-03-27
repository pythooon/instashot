<?php

declare(strict_types=1);

namespace App\Shared\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final readonly class ViolationListPresenter
{
    public static function toPlainText(ConstraintViolationListInterface $violations): string
    {
        /** @var list<string> $parts */
        $parts = [];
        foreach ($violations as $violation) {
            $path = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $parts[] = $path !== '' ? sprintf('%s: %s', $path, $message) : $message;
        }

        return implode(' ', $parts);
    }
}
