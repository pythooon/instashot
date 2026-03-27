<?php

declare(strict_types=1);

namespace Tests\Smoke\Auth\Controller;

use Tests\Smoke\Shared\SmokeWebTestCase;

final class AuthSmokeTest extends SmokeWebTestCase
{
    public function testLoginPageReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->pathForRoute('login'));

        self::assertResponseIsSuccessful();
    }

    public function testLogoutRedirectsToHome(): void
    {
        $client = static::createClient();
        $this->logInAsNatureLover($client);
        $crawler = $client->request('GET', $this->pathForRoute('profile'));
        self::assertResponseIsSuccessful();
        $form = $crawler->filter('form.profile-dropdown-logout')->form();
        $client->submit($form);

        $this->assertRedirectsToRoute('home');
    }

    public function testLoginSubmitWithUnknownValidHexTokenRedirectsToLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->pathForRoute('login'));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Zaloguj się')->form([
            'username' => 'nature_lover',
            'token' => str_repeat('ab', 32),
        ]);
        $client->submit($form);

        $this->assertRedirectsToRoute('login', ['username' => 'nature_lover']);
    }

    public function testLoginSubmitWithSeedCredentialsRedirectsToProfile(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->pathForRoute('login'));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Zaloguj się')->form([
            'username' => 'nature_lover',
            'token' => self::NATURE_LOVER_SEED_TOKEN,
        ]);
        $client->submit($form);

        self::assertResponseRedirects();
        $location = (string) $client->getResponse()->headers->get('Location', '');
        self::assertStringContainsString($this->pathForRoute('profile'), $location);
        self::assertStringContainsString('phoenix-import', $location);

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Profile', $client->getCrawler()->filter('title')->text());
    }

    public function testLoginSubmitWithInvalidTokenLengthReturnsUnprocessable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->pathForRoute('login'));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Zaloguj się')->form([
            'username' => 'nature_lover',
            'token' => 'short',
        ]);
        $client->submit($form);

        self::assertResponseStatusCodeSame(422);
    }
}
