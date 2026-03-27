<?php

declare(strict_types=1);

namespace App\Auth\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class TokenLoginRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Username is required.')]
        #[Assert\Length(max: 180, maxMessage: 'Username cannot be longer than {{ limit }} characters.')]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_.-]+$/',
            message: 'Username may only contain letters, digits, underscore, dot and hyphen.'
        )]
        private string $username,
        #[Assert\NotBlank(message: 'Token is required.')]
        #[Assert\Length(
            min: 32,
            max: 255,
            minMessage: 'Token is too short.',
            maxMessage: 'Token cannot be longer than {{ limit }} characters.'
        )]
        #[Assert\Regex(
            pattern: '/^[a-fA-F0-9]+$/',
            message: 'Token must be a hexadecimal string.'
        )]
        private string $token,
    ) {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
