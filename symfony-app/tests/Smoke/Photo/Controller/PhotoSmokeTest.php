<?php

declare(strict_types=1);

namespace Tests\Smoke\Photo\Controller;

use Tests\Smoke\Shared\SmokeWebTestCase;

/**
 * Smoke modułu Photo — strona główna (feed), polubienie zdjęcia.
 *
 * Strona główna odczytuje zdjęcia z bazy (jak w runtime).
 */
final class PhotoSmokeTest extends SmokeWebTestCase
{
    public function testHomeReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->pathForRoute('home'));

        self::assertResponseIsSuccessful();
    }

    public function testPhotoLikeWithoutSessionRedirectsToHome(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->pathForRoute('photo_like', ['photoId' => 1]));

        $this->assertRedirectsToRoute('home');
    }
}
