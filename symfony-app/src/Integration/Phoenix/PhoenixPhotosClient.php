<?php

declare(strict_types=1);

namespace App\Integration\Phoenix;

use App\Integration\Phoenix\Exception\PhoenixApiException;
use App\Integration\Phoenix\Exception\PhoenixApiUnauthorizedException;
use App\Integration\Phoenix\Mapper\PhoenixPhotoJsonMapper;
use App\Shared\Text\InputNormalizer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class PhoenixPhotosClient implements PhoenixPhotosClientInterface
{
    private const int REQUEST_TIMEOUT_SECONDS = 20;

    private const string PHOTOS_API_PATH = '/api/photos';

    public function __construct(
        private HttpClientInterface $httpClient,
        private PhoenixPhotoJsonMapper $photoJsonMapper,
        #[Autowire('%env(default:app.default_phoenix_url:PHOENIX_BASE_URL)%')]
        private string $phoenixBaseUrl,
    ) {
    }

    public function fetchPhotosForToken(string $accessToken): array
    {
        $accessToken = InputNormalizer::trimCopyPasteArtifacts($accessToken);
        if ($accessToken === '') {
            throw new PhoenixApiUnauthorizedException('Empty Phoenix access token.');
        }

        $response = $this->requestPhotos($accessToken);
        $this->assertPhotosResponseSuccess($response);

        try {
            /** @var array<string, mixed> $payload */
            $payload = $response->toArray();
        } catch (\Throwable $e) {
            throw new PhoenixApiException('Invalid JSON from Phoenix API.', 0, $e);
        }

        return $this->photoJsonMapper->mapPhotosPayload($payload);
    }

    private function requestPhotos(string $accessToken): ResponseInterface
    {
        $url = rtrim($this->phoenixBaseUrl, '/') . self::PHOTOS_API_PATH;

        try {
            return $this->httpClient->request('GET', $url, [
                'headers' => [
                    'access-token' => $accessToken,
                    'accept' => 'application/json',
                ],
                'timeout' => self::REQUEST_TIMEOUT_SECONDS,
            ]);
        } catch (\Throwable $e) {
            throw new PhoenixApiException('Could not reach Phoenix API: ' . $e->getMessage(), 0, $e);
        }
    }

    private function assertPhotosResponseSuccess(ResponseInterface $response): void
    {
        $status = $response->getStatusCode();
        if ($status === 401) {
            throw new PhoenixApiUnauthorizedException('Invalid or missing Phoenix access token.');
        }

        if ($status !== 200) {
            throw new PhoenixApiException('Phoenix API returned HTTP ' . $status . '.');
        }
    }
}
