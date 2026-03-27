<?php

declare(strict_types=1);

namespace App\Photo\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PhotoLikePayload
{
    public function __construct(
        #[SerializedName('_token')]
        private string $csrfToken = '',
        #[SerializedName('photoId')]
        #[Assert\Positive(message: 'Photo id must be a positive integer.')]
        private int $photoId = 0,
    ) {
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    public function getPhotoId(): int
    {
        return $this->photoId;
    }
}
