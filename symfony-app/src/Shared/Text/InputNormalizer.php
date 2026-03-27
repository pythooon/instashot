<?php

declare(strict_types=1);

namespace App\Shared\Text;

final class InputNormalizer
{
    public static function trimCopyPasteArtifacts(string $value): string
    {
        $trimmed = trim($value);
        $clean = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $trimmed);

        return $clean ?? $trimmed;
    }
}
