<?php

declare(strict_types=1);

namespace App\Profile\Dto;

use App\Profile\Enum\SavePhoenixAccessTokenOutcome;

final readonly class SavePhoenixAccessTokenResult
{
    private function __construct(
        private SavePhoenixAccessTokenOutcome $outcome,
    ) {
    }

    public static function success(): self
    {
        return new self(SavePhoenixAccessTokenOutcome::Success);
    }

    public static function userNotFound(): self
    {
        return new self(SavePhoenixAccessTokenOutcome::UserNotFound);
    }

    public static function invalidTokenFormat(): self
    {
        return new self(SavePhoenixAccessTokenOutcome::InvalidTokenFormat);
    }

    public function getOutcome(): SavePhoenixAccessTokenOutcome
    {
        return $this->outcome;
    }
}
