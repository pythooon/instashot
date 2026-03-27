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
        private int $page,
        private int $perPage,
        private int $totalPhotos,
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

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalPhotos(): int
    {
        return $this->totalPhotos;
    }

    public function getPageCount(): int
    {
        if ($this->totalPhotos === 0) {
            return 1;
        }

        return (int) ceil($this->totalPhotos / $this->perPage);
    }

    public function getRangeStart(): int
    {
        if ($this->totalPhotos === 0) {
            return 0;
        }

        return ($this->page - 1) * $this->perPage + 1;
    }

    public function getRangeEnd(): int
    {
        return min($this->page * $this->perPage, $this->totalPhotos);
    }
}
