<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Session;

use App\Shared\Session\SessionUserIdReader;
use PHPUnit\Framework\TestCase;

final class SessionUserIdReaderTest extends TestCase
{
    public function testReturnsIntWhenSessionStoresInt(): void
    {
        $this->assertSame(7, SessionUserIdReader::fromSessionValue(7));
    }

    public function testReturnsIntWhenSessionStoresNumericString(): void
    {
        $this->assertSame(42, SessionUserIdReader::fromSessionValue('42'));
    }

    public function testReturnsNullWhenMissingOrInvalid(): void
    {
        $this->assertNull(SessionUserIdReader::fromSessionValue(null));
        $this->assertNull(SessionUserIdReader::fromSessionValue(''));
        $this->assertNull(SessionUserIdReader::fromSessionValue('x'));
        $this->assertNull(SessionUserIdReader::fromSessionValue([]));
    }
}
