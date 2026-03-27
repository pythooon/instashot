<?php

declare(strict_types=1);

namespace App\Profile\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ImportPhoenixPhotosPayload
{
    public function __construct(
        #[SerializedName('_token')]
        private string $csrfToken = '',
    ) {
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
