<?php

declare(strict_types=1);

namespace App\Photo\Repository;

use App\Photo\Dto\HomeFeedFilterCriteria;
use App\Photo\Entity\Photo;
use App\Auth\Entity\User;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Photo>
 */
interface PhotoRepositoryInterface extends ObjectRepository
{
    /**
     * @return list<Photo>
     */
    public function findHomeFeedPhotos(HomeFeedFilterCriteria $criteria, int $limit, int $offset): array;

    public function countHomeFeedPhotos(HomeFeedFilterCriteria $criteria): int;

    public function userHasPhotoWithImageUrl(User $user, string $imageUrl): bool;
}
