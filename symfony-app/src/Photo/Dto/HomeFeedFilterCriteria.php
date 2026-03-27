<?php

declare(strict_types=1);

namespace App\Photo\Dto;

final readonly class HomeFeedFilterCriteria
{
    public function __construct(
        private ?string $locationSubstring,
        private ?string $cameraSubstring,
        private ?string $descriptionSubstring,
        private ?string $takenAtDay,
        private ?string $usernameSubstring,
    ) {
    }

    public function getLocationSubstring(): ?string
    {
        return $this->locationSubstring;
    }

    public function getCameraSubstring(): ?string
    {
        return $this->cameraSubstring;
    }

    public function getDescriptionSubstring(): ?string
    {
        return $this->descriptionSubstring;
    }

    public function getTakenAtDay(): ?string
    {
        return $this->takenAtDay;
    }

    public function getUsernameSubstring(): ?string
    {
        return $this->usernameSubstring;
    }

    public function hasActiveFilters(): bool
    {
        return $this->locationSubstring !== null
            || $this->cameraSubstring !== null
            || $this->descriptionSubstring !== null
            || $this->takenAtDay !== null
            || $this->usernameSubstring !== null;
    }
}
