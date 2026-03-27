<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Dto\Request;

use App\Photo\Dto\Request\PhotoLikePayload;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Reguły jak przy #[MapRequestPayload] na polubieniu (POST).
 */
final class PhotoLikePayloadValidationTest extends TestCase
{
    private function validator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testPositivePhotoIdIsValid(): void
    {
        $payload = new PhotoLikePayload(csrfToken: 'x', photoId: 1);
        $violations = $this->validator()->validate($payload);
        $this->assertCount(0, $violations);
    }

    public function testZeroPhotoIdFails(): void
    {
        $payload = new PhotoLikePayload(csrfToken: 'x', photoId: 0);
        $violations = $this->validator()->validate($payload);
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testNegativePhotoIdFails(): void
    {
        $payload = new PhotoLikePayload(csrfToken: 'x', photoId: -1);
        $violations = $this->validator()->validate($payload);
        $this->assertGreaterThan(0, $violations->count());
    }
}
