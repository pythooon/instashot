<?php

declare(strict_types=1);

namespace App\Profile\Controller;

use App\Profile\Service\ProfilePageServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfilePageServiceInterface $profilePageService,
    ) {
    }

    #[Route('/profile', name: 'profile')]
    public function profile(Request $request): Response
    {
        $session = $request->getSession();
        $userId = SessionUserIdReader::fromSessionValue($session->get('user_id'));
        if ($userId === null) {
            return $this->redirectToRoute('home');
        }

        $profile = $this->profilePageService->getProfileByUserId($userId);
        if ($profile === null) {
            $session->clear();

            return $this->redirectToRoute('home');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }
}
