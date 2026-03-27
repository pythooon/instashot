<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Profile\Dto\SavePhoenixAccessTokenResult;

interface SavePhoenixAccessTokenServiceInterface
{
    public function saveForUserId(
        int $userId,
        string $rawToken,
        bool $removeToken = false,
    ): SavePhoenixAccessTokenResult;
}
