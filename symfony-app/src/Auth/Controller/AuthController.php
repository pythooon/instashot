<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\Dto\Request\LoginPageQuery;
use App\Auth\Dto\Request\LoginSubmitPayload;
use App\Auth\Dto\Request\LogoutSubmitPayload;
use App\Auth\Dto\Response\AuthenticatedUserResponse;
use App\Auth\Enum\LoginFailureType;
use App\Auth\Service\TokenLoginServiceInterface;
use App\Shared\Session\SessionUserIdReader;
use App\Shared\Validation\ViolationListPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthController extends AbstractController
{
    private const CSRF_LOGIN = 'login_form';

    private const CSRF_LOGOUT = 'logout';

    public function __construct(
        private readonly TokenLoginServiceInterface $tokenLoginService,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/login', name: 'login', methods: ['GET'])]
    public function loginPage(
        SessionInterface $session,
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        LoginPageQuery $query = new LoginPageQuery(),
    ): Response {
        if (SessionUserIdReader::fromSessionValue($session->get('user_id')) !== null) {
            return $this->redirect($this->generateUrl('profile') . '#phoenix-import');
        }

        return $this->render('auth/login.html.twig', [
            'csrf_token_id' => self::CSRF_LOGIN,
            'last_username' => $query->getUsername() ?? '',
        ]);
    }

    #[Route('/login', name: 'login_submit', methods: ['POST'])]
    public function loginSubmit(
        SessionInterface $session,
        #[MapRequestPayload]
        LoginSubmitPayload $payload = new LoginSubmitPayload(),
    ): Response {
        if (!$this->isCsrfTokenValid(self::CSRF_LOGIN, $payload->getCsrfToken())) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $loginRequest = $payload->toTokenLoginRequest();

        $violations = $this->validator->validate($loginRequest);
        if (\count($violations) > 0) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ViolationListPresenter::toPlainText($violations),
                new ValidationFailedException($loginRequest, $violations),
            );
        }

        $result = $this->tokenLoginService->login($loginRequest);
        if (!$result->isSuccess()) {
            $failure = $result->getFailure();
            $message = $failure === LoginFailureType::InvalidToken
                ? 'Nieprawidłowy token.'
                : 'Nie znaleziono użytkownika lub username nie pasuje do tokenu.';
            $this->addFlash('error', $message);

            return $this->redirectToRoute('login', ['username' => $loginRequest->getUsername()]);
        }

        $user = $result->getUser();
        if ($user === null) {
            throw new \LogicException('Successful login must carry user payload.');
        }

        return $this->completeSessionLogin($session, $user);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(
        SessionInterface $session,
        #[MapRequestPayload]
        LogoutSubmitPayload $payload = new LogoutSubmitPayload(),
    ): Response {
        if (!$this->isCsrfTokenValid(self::CSRF_LOGOUT, $payload->getCsrfToken())) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $session->clear();

        $this->addFlash('info', 'You have been logged out successfully.');

        return $this->redirectToRoute('home');
    }

    private function completeSessionLogin(SessionInterface $session, AuthenticatedUserResponse $user): Response
    {
        $session->set('user_id', $user->getId());
        $session->set('username', $user->getUsername());

        $this->addFlash('success', 'Witaj, ' . $user->getUsername() . '!');

        return $this->redirect($this->generateUrl('profile') . '#phoenix-import');
    }
}
