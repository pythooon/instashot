<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Service;

use App\Auth\Dto\Request\TokenLoginRequest;
use App\Auth\Entity\User;
use App\Auth\Enum\LoginFailureType;
use App\Auth\Mapper\AuthenticatedUserMapper;
use App\Auth\Repository\AuthTokenRepositoryInterface;
use App\Auth\Service\TokenLoginService;
use PHPUnit\Framework\TestCase;

final class TokenLoginServiceTest extends TestCase
{
    public function testLoginFailsWhenTokenUnknown(): void
    {
        $repo = $this->createMock(AuthTokenRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('findUserByToken')
            ->with('unknown')
            ->willReturn(null);

        $service = new TokenLoginService(
            authTokenRepository: $repo,
            authenticatedUserMapper: new AuthenticatedUserMapper(),
        );
        $result = $service->login(new TokenLoginRequest(username: 'any', token: 'unknown'));

        $this->assertFalse($result->isSuccess());
        $this->assertSame(LoginFailureType::InvalidToken, $result->getFailure());
    }

    public function testLoginFailsWhenUsernameDoesNotMatchTokenOwner(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn('real_owner');

        $repo = $this->createMock(AuthTokenRepositoryInterface::class);
        $repo->method('findUserByToken')->willReturn($user);

        $service = new TokenLoginService(
            authTokenRepository: $repo,
            authenticatedUserMapper: new AuthenticatedUserMapper(),
        );
        $result = $service->login(new TokenLoginRequest(username: 'attacker', token: 'valid'));

        $this->assertFalse($result->isSuccess());
        $this->assertSame(LoginFailureType::UserNotFound, $result->getFailure());
    }

    public function testLoginSucceedsWhenTokenAndUsernameMatch(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn('emma');
        $user->method('getId')->willReturn(7);

        $repo = $this->createMock(AuthTokenRepositoryInterface::class);
        $repo->method('findUserByToken')->with('secret')->willReturn($user);

        $service = new TokenLoginService(
            authTokenRepository: $repo,
            authenticatedUserMapper: new AuthenticatedUserMapper(),
        );
        $result = $service->login(new TokenLoginRequest(username: 'emma', token: 'secret'));

        $this->assertTrue($result->isSuccess());
        $this->assertNotNull($result->getUser());
        $this->assertSame(7, $result->getUser()->getId());
        $this->assertSame('emma', $result->getUser()->getUsername());
    }
}
