<?php

declare(strict_types=1);

namespace App\Photo\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class PhotoLikeQuery
{
    public function __construct(
        #[Assert\Positive(message: 'Photo id must be a positive integer.')]
        private int $photoId,
    ) {
    }

    public function getPhotoId(): int
    {
        return $this->photoId;
    }
}
