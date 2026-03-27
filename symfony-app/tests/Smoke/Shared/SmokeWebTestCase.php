<?php

declare(strict_types=1);

namespace Tests\Smoke\Shared;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class SmokeWebTestCase extends WebTestCase
{
    protected const string NATURE_LOVER_SEED_TOKEN = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    protected function logInAsNatureLover(KernelBrowser $client): void
    {
        $crawler = $client->request('GET', $this->pathForRoute('login'));
        $form = $crawler->selectButton('Zaloguj się')->form([
            'username' => 'nature_lover',
            'token' => self::NATURE_LOVER_SEED_TOKEN,
        ]);
        $client->submit($form);
    }

    /**
     * @param array<string, scalar|list<scalar>> $parameters
     */
    protected function pathForRoute(
        string $route,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $router = self::getContainer()->get('router');
        if (!$router instanceof UrlGeneratorInterface) {
            throw new \LogicException('Router service must implement UrlGeneratorInterface.');
        }

        return $router->generate($route, $parameters, $referenceType);
    }

    /**
     * @param array<string, scalar|list<scalar>> $parameters
     */
    protected function assertRedirectsToRoute(string $route, array $parameters = []): void
    {
        $expected = $this->pathForRoute($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        self::assertResponseRedirects($expected);
    }
}
