<?php

declare(strict_types=1);

namespace App\Photo\Dto\Response;

final readonly class PhotoCardResponse
{
    public function __construct(
        private int $id,
        private string $imageUrl,
        private ?string $description,
        private ?string $location,
        private ?string $camera,
        private int $likeCounter,
        private UserBriefResponse $author,
        private bool $likedByCurrentUser,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getCamera(): ?string
    {
        return $this->camera;
    }

    public function getLikeCounter(): int
    {
        return $this->likeCounter;
    }

    public function getAuthor(): UserBriefResponse
    {
        return $this->author;
    }

    public function isLikedByCurrentUser(): bool
    {
        return $this->likedByCurrentUser;
    }
}
