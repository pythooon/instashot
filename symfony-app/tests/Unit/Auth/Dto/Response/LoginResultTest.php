<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Dto\Response;

use App\Auth\Dto\Response\AuthenticatedUserResponse;
use App\Auth\Dto\Response\LoginResult;
use App\Auth\Enum\LoginFailureType;
use PHPUnit\Framework\TestCase;

final class LoginResultTest extends TestCase
{
    public function testAuthenticatedCarriesUserAndNoFailure(): void
    {
        $user = new AuthenticatedUserResponse(id: 1, username: 'alice');
        $result = LoginResult::authenticated($user);

        $this->assertTrue($result->isSuccess());
        $this->assertSame($user, $result->getUser());
        $this->assertNull($result->getFailure());
    }

    public function testFailedCarriesFailureAndNoUser(): void
    {
        $result = LoginResult::failed(LoginFailureType::InvalidToken);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getUser());
        $this->assertSame(LoginFailureType::InvalidToken, $result->getFailure());
    }
}
