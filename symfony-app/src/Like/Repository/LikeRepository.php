<?php

declare(strict_types=1);

namespace App\Like\Repository;

use App\Auth\Entity\User;
use App\Like\Entity\Like;
use App\Photo\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
final class LikeRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function unlikePhoto(User $user, Photo $photo): void
    {
        $em = $this->getEntityManager();

        $like = $em->createQueryBuilder()
            ->select('l')
            ->from(Like::class, 'l')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $user)
            ->setParameter('photo', $photo)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($like instanceof Like) {
            $em->remove($like);
            $em->flush();

            $photo->setLikeCounter($photo->getLikeCounter() - 1);
            $em->persist($photo);
            $em->flush();
        }
    }

    public function hasUserLikedPhoto(User $user, Photo $photo): bool
    {
        /** @var list<array<string, mixed>> $likes */
        $likes = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $user)
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getArrayResult();

        return $likes !== [];
    }

    public function createLike(User $user, Photo $photo): Like
    {
        $like = new Like();
        $like->setUser($user);
        $like->setPhoto($photo);

        $em = $this->getEntityManager();
        $em->persist($like);
        $em->flush();

        return $like;
    }

    public function updatePhotoCounter(Photo $photo, int $increment): void
    {
        $em = $this->getEntityManager();
        $photo->setLikeCounter($photo->getLikeCounter() + $increment);
        $em->persist($photo);
        $em->flush();
    }
}
