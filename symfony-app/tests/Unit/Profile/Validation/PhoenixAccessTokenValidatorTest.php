<?php

declare(strict_types=1);

namespace Tests\Unit\Profile\Validation;

use App\Profile\Validation\PhoenixAccessTokenValidator;
use PHPUnit\Framework\TestCase;

final class PhoenixAccessTokenValidatorTest extends TestCase
{
    private PhoenixAccessTokenValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PhoenixAccessTokenValidator();
    }

    public function testAcceptsAlphanumericDotUnderscoreHyphen(): void
    {
        $this->assertTrue($this->validator->isValid('aB3.x_y-z'));
    }

    public function testRejectsEmptyString(): void
    {
        $this->assertFalse($this->validator->isValid(''));
    }

    public function testRejectsWhitespaceAndUnicode(): void
    {
        $this->assertFalse($this->validator->isValid('abc def'));
        $this->assertFalse($this->validator->isValid("tok\nen"));
        $this->assertFalse($this->validator->isValid('tökën'));
    }

    public function testRejectsTooLongToken(): void
    {
        $token = str_repeat('a', PhoenixAccessTokenValidator::MAX_LENGTH + 1);
        $this->assertFalse($this->validator->isValid($token));
    }

    public function testAcceptsMaxLengthToken(): void
    {
        $token = str_repeat('a', PhoenixAccessTokenValidator::MAX_LENGTH);
        $this->assertTrue($this->validator->isValid($token));
    }
}
