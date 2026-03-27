<?php

declare(strict_types=1);

namespace App\Auth\Repository;

use App\Auth\Entity\User;

interface AuthTokenRepositoryInterface
{
    public function findUserByToken(string $token): ?User;
}
