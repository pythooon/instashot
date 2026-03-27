<?php

declare(strict_types=1);

namespace App\Photo\Controller;

use App\Photo\Dto\Request\PhotoLikeQuery;
use App\Photo\Dto\Request\TogglePhotoLikeRequest;
use App\Photo\Enum\TogglePhotoLikeResultType;
use App\Photo\Service\PhotoLikeApplicationServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use App\Shared\Validation\ViolationListPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PhotoController extends AbstractController
{
    public function __construct(
        private readonly PhotoLikeApplicationServiceInterface $photoLikeApplicationService,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/photo/like', name: 'photo_like', methods: ['GET'])]
    public function like(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        PhotoLikeQuery $query,
        Request $request,
    ): Response {
        $session = $request->getSession();
        $userId = SessionUserIdReader::fromSessionValue($session->get('user_id'));
        if ($userId === null) {
            $this->addFlash('error', 'You must be logged in to like photos.');

            return $this->redirectToRoute('home');
        }

        $toggleRequest = new TogglePhotoLikeRequest(photoId: $query->getPhotoId(), userId: $userId);
        $violations = $this->validator->validate($toggleRequest);
        if ($violations->count() > 0) {
            $this->addFlash('error', ViolationListPresenter::toPlainText($violations));

            return $this->redirectToRoute('home');
        }

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
