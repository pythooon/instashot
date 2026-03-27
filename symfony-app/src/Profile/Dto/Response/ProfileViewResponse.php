<?php

declare(strict_types=1);

namespace App\Profile\Dto\Response;

final readonly class ProfileViewResponse
{
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private ?string $name,
        private ?string $lastName,
        private ?int $age,
        private ?string $bio,
        private int $photosCount,
        private bool $hasPhoenixAccessToken,
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getPhotosCount(): int
    {
        return $this->photosCount;
    }

    public function hasPhoenixAccessToken(): bool
    {
        return $this->hasPhoenixAccessToken;
    }
}
