<?php

declare(strict_types=1);

namespace App\Photo\Controller;

use App\Photo\Dto\Request\PhotoLikePayload;
use App\Photo\Dto\Request\TogglePhotoLikeRequest;
use App\Photo\Enum\TogglePhotoLikeResultType;
use App\Photo\Service\PhotoLikeApplicationServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

final class PhotoController extends AbstractController
{
    public const CSRF_PHOTO_LIKE = 'home_photo_like';

    public function __construct(
        private readonly PhotoLikeApplicationServiceInterface $photoLikeApplicationService,
    ) {
    }

    #[Route('/photo/like', name: 'photo_like', methods: ['POST'])]
    public function like(
        SessionInterface $session,
        #[MapRequestPayload]
        PhotoLikePayload $payload = new PhotoLikePayload(),
    ): Response {
        if (!$this->isCsrfTokenValid(self::CSRF_PHOTO_LIKE, $payload->getCsrfToken())) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $userId = SessionUserIdReader::fromSessionValue($session->get('user_id'));
        if ($userId === null) {
            $this->addFlash('error', 'You must be logged in to like photos.');

            return $this->redirectToRoute('home');
        }

        $toggleRequest = new TogglePhotoLikeRequest(photoId: $payload->getPhotoId(), userId: $userId);

        $result = $this->photoLikeApplicationService->togglePhotoLike($toggleRequest);

        return match ($result) {
            TogglePhotoLikeResultType::PhotoNotFound => throw $this->createNotFoundException('Photo not found'),
            TogglePhotoLikeResultType::UserNotFound => $this->redirectToRoute('home'),
            TogglePhotoLikeResultType::Unliked => $this->addFlashAndRedirect('info', 'Photo unliked!'),
            TogglePhotoLikeResultType::Liked => $this->addFlashAndRedirect('success', 'Photo liked!'),
        };
    }

    private function addFlashAndRedirect(string $type, string $message): Response
    {
        $this->addFlash($type, $message);

        return $this->redirectToRoute('home');
    }
}
