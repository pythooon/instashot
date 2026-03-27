<?php

declare(strict_types=1);

namespace Tests\Smoke\Profile\Controller;

use Tests\Smoke\Shared\SmokeWebTestCase;

/**
 * Smoke modułu Profile — widok profilu (wymaga sesji).
 */
final class ProfileSmokeTest extends SmokeWebTestCase
{
    public function testProfileWithoutSessionRedirectsToHome(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->pathForRoute('profile'));

        $this->assertRedirectsToRoute('home');
    }
}
