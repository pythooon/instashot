<?php

declare(strict_types=1);

namespace App\Profile\Service;

use App\Profile\Dto\ImportPhoenixPhotosResult;

interface ImportPhoenixPhotosServiceInterface
{
    public function importForUserId(int $userId): ImportPhoenixPhotosResult;
}
