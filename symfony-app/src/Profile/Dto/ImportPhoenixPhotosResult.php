<?php

declare(strict_types=1);

namespace App\Profile\Dto;

use App\Profile\Enum\ImportPhoenixPhotosOutcome;

final readonly class ImportPhoenixPhotosResult
{
    private function __construct(
        private ImportPhoenixPhotosOutcome $outcome,
        private int $importedCount,
    ) {
    }

    public static function success(int $importedCount): self
    {
        return new self(ImportPhoenixPhotosOutcome::Success, $importedCount);
    }

    public static function noTokenConfigured(): self
    {
        return new self(ImportPhoenixPhotosOutcome::NoTokenConfigured, 0);
    }

    public static function invalidToken(): self
    {
        return new self(ImportPhoenixPhotosOutcome::InvalidToken, 0);
    }

    public static function userNotFound(): self
    {
        return new self(ImportPhoenixPhotosOutcome::UserNotFound, 0);
    }

    public static function requestFailed(): self
    {
        return new self(ImportPhoenixPhotosOutcome::RequestFailed, 0);
    }

    public function getOutcome(): ImportPhoenixPhotosOutcome
    {
        return $this->outcome;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
