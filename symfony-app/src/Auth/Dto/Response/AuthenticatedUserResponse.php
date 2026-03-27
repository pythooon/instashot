<?php

declare(strict_types=1);

namespace App\Auth\Dto\Response;

final readonly class AuthenticatedUserResponse
{
    public function __construct(
        private int $id,
        private string $username,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
