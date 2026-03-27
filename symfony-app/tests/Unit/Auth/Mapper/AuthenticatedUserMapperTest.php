<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Mapper;

use App\Auth\Entity\User;
use App\Auth\Mapper\AuthenticatedUserMapper;
use PHPUnit\Framework\TestCase;

final class AuthenticatedUserMapperTest extends TestCase
{
    public function testMapsPersistedUserToResponse(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(42);
        $user->method('getUsername')->willReturn('nature_lover');

        $dto = (new AuthenticatedUserMapper())->toAuthenticatedUser($user);

        $this->assertSame(42, $dto->getId());
        $this->assertSame('nature_lover', $dto->getUsername());
    }

    public function testThrowsWhenUserNotPersisted(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(null);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('persisted');

        (new AuthenticatedUserMapper())->toAuthenticatedUser($user);
    }
}
