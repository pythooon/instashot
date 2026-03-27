<?php

declare(strict_types=1);

namespace App\Photo\Dto\Request;

final readonly class TogglePhotoLikeRequest
{
    public function __construct(
        private int $photoId,
        private int $userId,
    ) {
    }

    public function getPhotoId(): int
    {
        return $this->photoId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
