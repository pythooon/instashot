<?php

declare(strict_types=1);

namespace App\Photo\Dto\Response;

final readonly class HomeFeedViewResponse
{
    /**
     * @param list<PhotoCardResponse> $photos
     */
    public function __construct(
        private array $photos,
        private ?UserBriefResponse $currentUser,
    ) {
    }

    /**
     * @return list<PhotoCardResponse>
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function getCurrentUser(): ?UserBriefResponse
    {
        return $this->currentUser;
    }
}
