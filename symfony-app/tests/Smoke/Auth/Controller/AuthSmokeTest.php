<?php

declare(strict_types=1);

namespace Tests\Smoke\Auth\Controller;

use Tests\Smoke\Shared\SmokeWebTestCase;

/**
 * Smoke modułu Auth — logowanie tokenem, wylogowanie.
 */
final class AuthSmokeTest extends SmokeWebTestCase
{
    public function testLogoutRedirectsToHome(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->pathForRoute('logout'));

        $this->assertRedirectsToRoute('home');
    }

    public function testLoginWithUnknownValidHexTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $token = str_repeat('ab', 32);
        $client->request(
            'GET',
            $this->pathForRoute('auth_login', ['username' => 'nature_lover', 'token' => $token])
        );

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoginWithInvalidTokenDtoReturnsBadRequest(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            $this->pathForRoute('auth_login', ['username' => 'nature_lover', 'token' => 'short'])
        );

        self::assertResponseStatusCodeSame(400);
    }
}
