<?php

declare(strict_types=1);

namespace App\Auth\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class LoginSubmitPayload
{
    public function __construct(
        #[SerializedName('_token')]
        private string $csrfToken = '',
        #[SerializedName('username')]
        private string $username = '',
        #[SerializedName('token')]
        private string $token = '',
    ) {
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    public function toTokenLoginRequest(): TokenLoginRequest
    {
        $username = trim($this->username);
        $token = trim($this->token);
        $cleaned = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $token);

        return new TokenLoginRequest(username: $username, token: $cleaned ?? '');
    }
}
