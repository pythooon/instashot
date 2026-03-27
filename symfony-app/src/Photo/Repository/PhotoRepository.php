<?php

declare(strict_types=1);

namespace App\Photo\Repository;

use App\Photo\Dto\HomeFeedFilterCriteria;
use App\Photo\Entity\Photo;
use App\Auth\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 */
final class PhotoRepository extends ServiceEntityRepository implements PhotoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * @return list<Photo>
     */
    public function findHomeFeedPhotos(HomeFeedFilterCriteria $criteria, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('p')->orderBy('p.id', 'ASC');
        $this->joinUserForUsernameFilter($qb, $criteria);
        if ($criteria->hasActiveFilters()) {
            $this->applyHomeFeedFilterCriteria($qb, $criteria);
        }
        $qb->setFirstResult($offset)->setMaxResults($limit);

        /** @var list<Photo> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function countHomeFeedPhotos(HomeFeedFilterCriteria $criteria): int
    {
        $qb = $this->createQueryBuilder('p')->select('COUNT(p.id)');
        $this->joinUserForUsernameFilter($qb, $criteria);
        if ($criteria->hasActiveFilters()) {
            $this->applyHomeFeedFilterCriteria($qb, $criteria);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function joinUserForUsernameFilter(QueryBuilder $qb, HomeFeedFilterCriteria $criteria): void
    {
        if ($criteria->getUsernameSubstring() !== null) {
            $qb->leftJoin('p.user', 'u');
        }
    }

    private function applyHomeFeedFilterCriteria(QueryBuilder $qb, HomeFeedFilterCriteria $criteria): void
    {
        if ($criteria->getLocationSubstring() !== null) {
            $qb->andWhere('LOWER(p.location) LIKE :location')
                ->setParameter(
                    'location',
                    '%' . mb_strtolower($criteria->getLocationSubstring()) . '%',
                );
        }

        if ($criteria->getCameraSubstring() !== null) {
            $qb->andWhere('LOWER(p.camera) LIKE :camera')
                ->setParameter(
                    'camera',
                    '%' . mb_strtolower($criteria->getCameraSubstring()) . '%',
                );
        }

        if ($criteria->getDescriptionSubstring() !== null) {
            $qb->andWhere('LOWER(p.description) LIKE :description')
                ->setParameter(
                    'description',
                    '%' . mb_strtolower($criteria->getDescriptionSubstring()) . '%',
                );
        }

        if ($criteria->getUsernameSubstring() !== null) {
            $qb->andWhere('LOWER(u.username) LIKE :username')
                ->setParameter(
                    'username',
                    '%' . mb_strtolower($criteria->getUsernameSubstring()) . '%',
                );
        }

        if ($criteria->getTakenAtDay() !== null) {
            $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $criteria->getTakenAtDay());
            if ($day !== false) {
                $start = $day->setTime(0, 0, 0);
                $end = $day->modify('+1 day')->setTime(0, 0, 0);
                $qb->andWhere('p.takenAt IS NOT NULL')
                    ->andWhere('p.takenAt >= :takenStart')
                    ->andWhere('p.takenAt < :takenEnd')
                    ->setParameter('takenStart', $start)
                    ->setParameter('takenEnd', $end);
            }
        }
    }

    public function userHasPhotoWithImageUrl(User $user, string $imageUrl): bool
    {
        $count = (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.user = :user')
            ->andWhere('p.imageUrl = :url')
            ->setParameter('user', $user)
            ->setParameter('url', $imageUrl)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
