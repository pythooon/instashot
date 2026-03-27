<?php

declare(strict_types=1);

namespace App\Photo\Dto\Response;

final readonly class UserBriefResponse
{
    public function __construct(
        private int $id,
        private string $username,
        private ?string $name,
        private ?string $lastName,
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
