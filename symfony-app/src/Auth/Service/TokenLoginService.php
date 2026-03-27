<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Dto\Request\TokenLoginRequest;
use App\Auth\Dto\Response\LoginResult;
use App\Auth\Enum\LoginFailureType;
use App\Auth\Mapper\AuthenticatedUserMapper;
use App\Auth\Repository\AuthTokenRepositoryInterface;

final readonly class TokenLoginService implements TokenLoginServiceInterface
{
    public function __construct(
        private AuthTokenRepositoryInterface $authTokenRepository,
        private AuthenticatedUserMapper $authenticatedUserMapper,
    ) {
    }

    public function login(TokenLoginRequest $request): LoginResult
    {
        $user = $this->authTokenRepository->findUserByToken($request->getToken());
        if ($user === null) {
            return LoginResult::failed(LoginFailureType::InvalidToken);
        }

        if ($user->getUsername() !== $request->getUsername()) {
            return LoginResult::failed(LoginFailureType::UserNotFound);
        }

        return LoginResult::authenticated(
            $this->authenticatedUserMapper->toAuthenticatedUser($user)
        );
    }
}
