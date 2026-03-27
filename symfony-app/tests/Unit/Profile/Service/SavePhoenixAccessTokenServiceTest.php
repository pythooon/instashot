<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Service;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepositoryInterface;
use App\Profile\Enum\SavePhoenixAccessTokenOutcome;
use App\Profile\Service\SavePhoenixAccessTokenService;
use App\Profile\Validation\PhoenixAccessTokenValidator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class SavePhoenixAccessTokenServiceTest extends TestCase
{
    public function testUserNotFound(): void
    {
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(99)->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('flush');

        $service = new SavePhoenixAccessTokenService(
            $userRepo,
            $em,
            new PhoenixAccessTokenValidator(),
        );

        $r = $service->saveForUserId(99, 'any', false);
        $this->assertSame(SavePhoenixAccessTokenOutcome::UserNotFound, $r->getOutcome());
    }

    public function testRemoveTokenClearsAndFlushes(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setPhoenixAccessToken')->with(null);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $service = new SavePhoenixAccessTokenService(
            $userRepo,
            $em,
            new PhoenixAccessTokenValidator(),
        );

        $r = $service->saveForUserId(1, 'ignored', true);
        $this->assertSame(SavePhoenixAccessTokenOutcome::Success, $r->getOutcome());
    }

    public function testWhitespaceOnlySucceedsWithoutFlush(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->never())->method('setPhoenixAccessToken');

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('flush');

        $service = new SavePhoenixAccessTokenService(
            $userRepo,
            $em,
            new PhoenixAccessTokenValidator(),
        );

        $r = $service->saveForUserId(1, "  \t  ", false);
        $this->assertSame(SavePhoenixAccessTokenOutcome::Success, $r->getOutcome());
    }

    public function testInvalidFormatDoesNotPersist(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->never())->method('setPhoenixAccessToken');

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('flush');

        $service = new SavePhoenixAccessTokenService(
            $userRepo,
            $em,
            new PhoenixAccessTokenValidator(),
        );

        $r = $service->saveForUserId(1, 'bad token!', false);
        $this->assertSame(SavePhoenixAccessTokenOutcome::InvalidTokenFormat, $r->getOutcome());
    }

    public function testValidTokenNormalizedAndFlushed(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setPhoenixAccessToken')->with('ok.token_1');

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('find')->with(1)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $service = new SavePhoenixAccessTokenService(
            $userRepo,
            $em,
            new PhoenixAccessTokenValidator(),
        );

        $r = $service->saveForUserId(1, '  ok.token_1  ', false);
        $this->assertSame(SavePhoenixAccessTokenOutcome::Success, $r->getOutcome());
    }
}
