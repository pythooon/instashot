<?php

declare(strict_types=1);

namespace App\Auth\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class LogoutSubmitPayload
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
