<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\Dto\Request;

use App\Auth\Dto\Request\TokenLoginRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TokenLoginRequestValidationTest extends TestCase
{
    private function validator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testValidRequestHasNoViolations(): void
    {
        $token = str_repeat('ab', 32);
        $violations = $this->validator()->validate(new TokenLoginRequest(username: 'nature_lover', token: $token));
        $this->assertCount(0, $violations);
    }

    public function testBlankUsernameFails(): void
    {
        $violations = $this->validator()->validate(
            new TokenLoginRequest(username: '', token: str_repeat('ab', 32))
        );
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testInvalidCharactersInUsernameFail(): void
    {
        $violations = $this->validator()->validate(
            new TokenLoginRequest(username: 'bad name', token: str_repeat('ab', 32))
        );
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testTokenTooShortFails(): void
    {
        $violations = $this->validator()->validate(new TokenLoginRequest(username: 'user', token: 'abcd'));
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testNonHexTokenFails(): void
    {
        $violations = $this->validator()->validate(new TokenLoginRequest(username: 'user', token: str_repeat('z', 32)));
        $this->assertGreaterThan(0, $violations->count());
    }
}
