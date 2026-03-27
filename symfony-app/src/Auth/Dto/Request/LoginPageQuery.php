<?php

declare(strict_types=1);

namespace App\Auth\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginPageQuery
{
    public function __construct(
        #[Assert\Length(max: 180)]
        private ?string $username = null,
    ) {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
