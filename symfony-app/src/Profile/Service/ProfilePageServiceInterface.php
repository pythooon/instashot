<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Profile\Dto\Response\ProfileViewResponse;

interface ProfilePageServiceInterface
{
    public function getProfileByUserId(int $userId): ?ProfileViewResponse;
}
