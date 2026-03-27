<?php

declare(strict_types=1);

namespace App\Auth\Dto\Response;

use App\Auth\Enum\LoginFailureType;

final readonly class LoginResult
{
    private function __construct(
        private bool $success,
        private ?AuthenticatedUserResponse $user,
        private ?LoginFailureType $failure,
    ) {
    }

    public static function authenticated(AuthenticatedUserResponse $user): self
    {
        return new self(success: true, user: $user, failure: null);
    }

    public static function failed(LoginFailureType $failure): self
    {
        return new self(success: false, user: null, failure: $failure);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getUser(): ?AuthenticatedUserResponse
    {
        return $this->user;
    }

    public function getFailure(): ?LoginFailureType
    {
        return $this->failure;
    }
}
