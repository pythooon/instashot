<?php

declare(strict_types=1);

namespace App\Auth\Repository;

use App\Auth\Entity\AuthToken;
use App\Auth\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthToken>
 */
final class AuthTokenRepository extends ServiceEntityRepository implements AuthTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthToken::class);
    }

    public function findUserByToken(string $token): ?User
    {
        $authToken = $this->createQueryBuilder('at')
            ->innerJoin('at.user', 'u')
            ->addSelect('u')
            ->where('LOWER(at.token) = LOWER(:token)')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();

        return $authToken instanceof AuthToken ? $authToken->getUser() : null;
    }
}
