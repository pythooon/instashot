<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\Dto\Request\TokenLoginRequest;
use App\Auth\Enum\LoginFailureType;
use App\Auth\Service\TokenLoginServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly TokenLoginServiceInterface $tokenLoginService,
    ) {
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['GET'])]
    public function login(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        TokenLoginRequest $loginRequest,
        Request $request,
    ): Response {
        $result = $this->tokenLoginService->login($loginRequest);

        if (!$result->isSuccess()) {
            $failure = $result->getFailure();
            $status = $failure === LoginFailureType::InvalidToken ? 401 : 404;
            $message = $failure === LoginFailureType::InvalidToken
                ? 'Invalid token'
                : 'User not found';

            return new Response(content: $message, status: $status);
        }

        $user = $result->getUser();
        if ($user === null) {
            throw new \LogicException('Successful login must carry user payload.');
        }

        $session = $request->getSession();
        $session->set('user_id', $user->getId());
        $session->set('username', $user->getUsername());

        $this->addFlash('success', 'Welcome back, ' . $user->getUsername() . '!');

        return $this->redirectToRoute('home');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->clear();

        $this->addFlash('info', 'You have been logged out successfully.');

        return $this->redirectToRoute('home');
    }
}
