<?php

declare(strict_types=1);

namespace Tests\Smoke\Photo\Controller;

use Tests\Smoke\Shared\SmokeWebTestCase;

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
        $this->logInAsNatureLover($client);
        $crawler = $client->request('GET', $this->pathForRoute('home'));
        self::assertResponseIsSuccessful();

        $request = $client->getRequest();
        self::assertNotNull($request);
        $session = $request->getSession();
        $session->remove('user_id');
        $session->remove('username');
        $session->save();

        $form = $crawler->filter('form.photo-like-form')->first()->form();
        $client->submit($form);

        $this->assertRedirectsToRoute('home');
    }
}
