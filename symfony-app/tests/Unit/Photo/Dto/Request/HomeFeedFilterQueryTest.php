<?php

declare(strict_types=1);

namespace Tests\Unit\Photo\Dto\Request;

use App\Photo\Dto\Request\HomeFeedFilterQuery;
use PHPUnit\Framework\TestCase;

final class HomeFeedFilterQueryTest extends TestCase
{
    public function testToCriteriaTrimsAndNullsEmptyStrings(): void
    {
        $q = new HomeFeedFilterQuery(
            location: '  Beach  ',
            camera: '',
            description: null,
            takenAt: '2024-06-01',
            username: '  alice  ',
        );
        $c = $q->toCriteria();

        $this->assertSame('Beach', $c->getLocationSubstring());
        $this->assertNull($c->getCameraSubstring());
        $this->assertNull($c->getDescriptionSubstring());
        $this->assertSame('2024-06-01', $c->getTakenAtDay());
        $this->assertSame('alice', $c->getUsernameSubstring());
    }
}
