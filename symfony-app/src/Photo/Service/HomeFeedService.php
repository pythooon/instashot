<?php

declare(strict_types=1);

namespace App\Photo\Service;

use App\Like\Service\LikeServiceInterface;
use App\Photo\Dto\Request\HomeFeedFilterQuery;
use App\Photo\Dto\Response\HomeFeedViewResponse;
use App\Photo\Dto\Response\PhotoCardResponse;
use App\Photo\Mapper\PhotoFeedMapper;
use App\Photo\Repository\PhotoRepositoryInterface;
use App\Auth\Repository\UserRepositoryInterface;

final readonly class HomeFeedService implements HomeFeedServiceInterface
{
    private const int PER_PAGE = 12;

    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private UserRepositoryInterface $userRepository,
        private LikeServiceInterface $likeService,
        private PhotoFeedMapper $photoFeedMapper,
    ) {
    }

    public function buildHomeFeed(?int $currentUserId, HomeFeedFilterQuery $filterQuery): HomeFeedViewResponse
    {
        $criteria = $filterQuery->toCriteria();
        $totalPhotos = $this->photoRepository->countHomeFeedPhotos($criteria);
        $perPage = self::PER_PAGE;
        $pageCount = max(1, (int) ceil($totalPhotos / $perPage));
        $page = min(max(1, $filterQuery->getPage()), $pageCount);
        $offset = ($page - 1) * $perPage;
        $photos = $this->photoRepository->findHomeFeedPhotos($criteria, $perPage, $offset);
        $currentUserEntity = $currentUserId !== null
            ? $this->userRepository->find($currentUserId)
            : null;

        $currentBrief = $currentUserEntity !== null
            ? $this->photoFeedMapper->toUserBrief($currentUserEntity)
            : null;

        /** @var list<PhotoCardResponse> $cards */
        $cards = [];
        foreach ($photos as $photo) {
            $liked = $currentUserEntity !== null
                && $this->likeService->hasUserLikedPhoto($currentUserEntity, $photo);
            $cards[] = $this->photoFeedMapper->toPhotoCard($photo, $liked);
        }

        return new HomeFeedViewResponse(
            photos: $cards,
            currentUser: $currentBrief,
            page: $page,
            perPage: $perPage,
            totalPhotos: $totalPhotos,
        );
    }
}
