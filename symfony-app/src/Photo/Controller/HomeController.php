<?php

declare(strict_types=1);

namespace App\Photo\Controller;

use App\Photo\Dto\Request\HomeFeedFilterQuery;
use App\Photo\Service\HomeFeedServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly HomeFeedServiceInterface $homeFeedService,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(
        SessionInterface $session,
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        HomeFeedFilterQuery $filterQuery = new HomeFeedFilterQuery(),
    ): Response {
        $currentUserId = SessionUserIdReader::fromSessionValue($session->get('user_id'));

        $feed = $this->homeFeedService->buildHomeFeed($currentUserId, $filterQuery);

        return $this->render('home/index.html.twig', [
            'feed' => $feed,
            'filter' => $filterQuery,
            'homeFeedFiltersActive' => $filterQuery->toCriteria()->hasActiveFilters(),
            'photo_like_csrf' => PhotoController::CSRF_PHOTO_LIKE,
        ]);
    }
}
