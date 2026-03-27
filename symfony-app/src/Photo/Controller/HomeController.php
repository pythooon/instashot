<?php

declare(strict_types=1);

namespace App\Photo\Controller;

use App\Photo\Service\HomeFeedServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly HomeFeedServiceInterface $homeFeedService,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $currentUserId = SessionUserIdReader::fromSessionValue($session->get('user_id'));

        $feed = $this->homeFeedService->buildHomeFeed($currentUserId);

        return $this->render('home/index.html.twig', [
            'feed' => $feed,
        ]);
    }
}
