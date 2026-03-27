<?php

declare(strict_types=1);

namespace App\Photo\Repository;

use App\Photo\Entity\Photo;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends ObjectRepository<Photo>
 */
interface PhotoRepositoryInterface extends ObjectRepository
{
    /**
     * @return list<Photo>
     */
    public function findAllWithUsers(): array;
}
