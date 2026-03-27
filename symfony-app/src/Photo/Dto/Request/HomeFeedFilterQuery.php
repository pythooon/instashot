<?php

declare(strict_types=1);

namespace App\Photo\Dto\Request;

use App\Photo\Dto\HomeFeedFilterCriteria;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class HomeFeedFilterQuery
{
    public function __construct(
        #[Assert\Length(max: 255)]
        private ?string $location = null,
        #[Assert\Length(max: 255)]
        private ?string $camera = null,
        #[Assert\Length(max: 500)]
        private ?string $description = null,
        #[Assert\AtLeastOneOf([
            new Assert\Blank(),
            new Assert\Regex(pattern: '/^\d{4}-\d{2}-\d{2}$/', message: 'Use YYYY-MM-DD for takenAt.'),
        ])]
        private ?string $takenAt = null,
        #[Assert\Length(max: 180)]
        private ?string $username = null,
        #[Assert\Positive(message: 'Page must be at least 1.')]
        private int $page = 1,
    ) {
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getCamera(): ?string
    {
        return $this->camera;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTakenAt(): ?string
    {
        return $this->takenAt;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function toCriteria(): HomeFeedFilterCriteria
    {
        return new HomeFeedFilterCriteria(
            locationSubstring: $this->trimOrNull($this->location),
            cameraSubstring: $this->trimOrNull($this->camera),
            descriptionSubstring: $this->trimOrNull($this->description),
            takenAtDay: $this->trimOrNull($this->takenAt),
            usernameSubstring: $this->trimOrNull($this->username),
        );
    }

    private function trimOrNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
