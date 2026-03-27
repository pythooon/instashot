<?php

declare(strict_types=1);

namespace App\Profile\Controller;

use App\Profile\Dto\Request\ImportPhoenixPhotosPayload;
use App\Profile\Dto\Request\SavePhoenixTokenPayload;
use App\Profile\Enum\ImportPhoenixPhotosOutcome;
use App\Profile\Enum\SavePhoenixAccessTokenOutcome;
use App\Profile\Service\ImportPhoenixPhotosServiceInterface;
use App\Profile\Service\ProfilePageServiceInterface;
use App\Profile\Service\SavePhoenixAccessTokenServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use App\Shared\Text\InputNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    private const CSRF_PHOENIX_TOKEN = 'profile_phoenix_token';
    private const CSRF_IMPORT = 'profile_phoenix_import';

    public function __construct(
        private readonly ProfilePageServiceInterface $profilePageService,
        private readonly SavePhoenixAccessTokenServiceInterface $savePhoenixAccessTokenService,
        private readonly ImportPhoenixPhotosServiceInterface $importPhoenixPhotosService,
    ) {
    }

    #[Route('/profile', name: 'profile')]
    public function profile(SessionInterface $session): Response
    {
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
            'phoenix_token_csrf' => self::CSRF_PHOENIX_TOKEN,
            'phoenix_import_csrf' => self::CSRF_IMPORT,
        ]);
    }

    #[Route('/profile/phoenix-token', name: 'profile_phoenix_token_save', methods: ['POST'])]
    public function savePhoenixToken(
        SessionInterface $session,
        #[MapRequestPayload]
        SavePhoenixTokenPayload $payload = new SavePhoenixTokenPayload(),
    ): Response {
        $userId = SessionUserIdReader::fromSessionValue($session->get('user_id'));
        if ($userId === null) {
            return $this->redirectToRoute('home');
        }

        if (!$this->isCsrfTokenValid(self::CSRF_PHOENIX_TOKEN, $payload->getCsrfToken())) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $raw = $payload->getPhoenixAccessToken();
        $remove = $payload->shouldRemoveToken();

        $result = $this->savePhoenixAccessTokenService->saveForUserId($userId, $raw, $remove);

        match ($result->getOutcome()) {
            SavePhoenixAccessTokenOutcome::UserNotFound => $session->clear(),
            SavePhoenixAccessTokenOutcome::InvalidTokenFormat => $this->addFlash(
                'error',
                'Nieprawidłowy format tokenu.',
            ),
            SavePhoenixAccessTokenOutcome::Success => $this->applySavePhoenixTokenSuccessFlash($remove, $raw),
        };

        if ($result->getOutcome() === SavePhoenixAccessTokenOutcome::UserNotFound) {
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('profile');
    }

    #[Route('/profile/import-phoenix', name: 'profile_import_phoenix', methods: ['POST'])]
    public function importPhoenixPhotos(
        SessionInterface $session,
        #[MapRequestPayload]
        ImportPhoenixPhotosPayload $payload = new ImportPhoenixPhotosPayload(),
    ): Response {
        $userId = SessionUserIdReader::fromSessionValue($session->get('user_id'));
        if ($userId === null) {
            return $this->redirectToRoute('home');
        }

        if (!$this->isCsrfTokenValid(self::CSRF_IMPORT, $payload->getCsrfToken())) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $result = $this->importPhoenixPhotosService->importForUserId($userId);

        match ($result->getOutcome()) {
            ImportPhoenixPhotosOutcome::Success => $this->addFlash(
                'success',
                $result->getImportedCount() > 0
                    ? 'Zaimportowano ' . $result->getImportedCount() . ' zdjęć.'
                    : 'Brak nowych zdjęć do zaimportowania.',
            ),
            ImportPhoenixPhotosOutcome::NoTokenConfigured => $this->addFlash(
                'warning',
                'Najpierw zapisz token.',
            ),
            ImportPhoenixPhotosOutcome::InvalidToken => $this->addFlash(
                'error',
                'Nie udało się zaimportować zdjęć.',
            ),
            ImportPhoenixPhotosOutcome::UserNotFound => $this->addFlash('error', 'Nie znaleziono użytkownika.'),
            ImportPhoenixPhotosOutcome::RequestFailed => $this->addFlash(
                'error',
                'Usługa jest chwilowo niedostępna. Spróbuj później.',
            ),
        };

        return $this->redirectToRoute('profile');
    }

    private function applySavePhoenixTokenSuccessFlash(bool $remove, string $raw): void
    {
        if ($remove) {
            $this->addFlash('success', 'Usunięto token.');

            return;
        }

        if (InputNormalizer::trimCopyPasteArtifacts($raw) !== '') {
            $this->addFlash('success', 'Zapisano.');
        }
    }
}
