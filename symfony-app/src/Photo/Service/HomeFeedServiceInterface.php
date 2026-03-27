<?php

declare(strict_types=1);

namespace App\Photo\Service;

use App\Photo\Dto\Request\HomeFeedFilterQuery;
use App\Photo\Dto\Response\HomeFeedViewResponse;

interface HomeFeedServiceInterface
{
    public function buildHomeFeed(?int $currentUserId, HomeFeedFilterQuery $filterQuery): HomeFeedViewResponse;
}
