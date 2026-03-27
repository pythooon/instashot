<?php

declare(strict_types=1);

namespace App\Integration\Phoenix;

use App\Integration\Phoenix\Dto\PhoenixPhotoData;

interface PhoenixPhotosClientInterface
{
    /**
     * @return list<PhoenixPhotoData>
     */
    public function fetchPhotosForToken(string $accessToken): array;
}
