<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Dto\Request;

use App\Photo\Dto\Request\TogglePhotoLikeRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TogglePhotoLikeRequestValidationTest extends TestCase
{
    private function validator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testPositiveIdsAreValid(): void
    {
        $violations = $this->validator()->validate(new TogglePhotoLikeRequest(photoId: 1, userId: 2));
        $this->assertCount(0, $violations);
    }

    public function testZeroPhotoIdFails(): void
    {
        $violations = $this->validator()->validate(new TogglePhotoLikeRequest(photoId: 0, userId: 1));
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testZeroUserIdFails(): void
    {
        $violations = $this->validator()->validate(new TogglePhotoLikeRequest(photoId: 5, userId: 0));
        $this->assertGreaterThan(0, $violations->count());
    }

    public function testNegativeIdsFail(): void
    {
        $violations = $this->validator()->validate(new TogglePhotoLikeRequest(photoId: -1, userId: -2));
        $this->assertGreaterThanOrEqual(2, $violations->count());
    }
}
