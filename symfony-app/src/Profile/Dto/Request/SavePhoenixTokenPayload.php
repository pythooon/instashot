<?php

declare(strict_types=1);

namespace App\Profile\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class SavePhoenixTokenPayload
{
    public function __construct(
        #[SerializedName('_token')]
        private string $csrfToken = '',
        #[SerializedName('phoenix_access_token')]
        private string $phoenixAccessToken = '',
        #[SerializedName('remove_phoenix_token')]
        private ?string $removePhoenixToken = null,
    ) {
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    public function getPhoenixAccessToken(): string
    {
        return $this->phoenixAccessToken;
    }

    public function shouldRemoveToken(): bool
    {
        return $this->removePhoenixToken !== null && $this->removePhoenixToken !== '';
    }
}
