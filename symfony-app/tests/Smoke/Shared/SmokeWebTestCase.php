<?php

declare(strict_types=1);

namespace Tests\Smoke\Shared;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Bazowy WebTestCase dla smoke: URL z nazw tras (bez sztywnych ścieżek).
 */
abstract class SmokeWebTestCase extends WebTestCase
{
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
