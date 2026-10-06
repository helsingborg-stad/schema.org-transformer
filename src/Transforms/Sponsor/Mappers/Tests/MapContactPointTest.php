<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapContactPoint;

#[CoversClass(MapContactPoint::class)]
final class MapContactPointTest extends TestCase
{
    /** Verify that contact details come from the demand fixture's organization fields. */
    public function testMapsContactDetails(): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/demand.data.json'), true, 512, JSON_THROW_ON_ERROR)[0];

        $contactPoint = (new MapContactPoint())->map($data);

        self::assertEquals(
            Schema::contactPoint()
                ->name('Exempel Kontakt')
                ->role('Kontaktperson')
                ->email('kontakt@example.invalid')
                ->telephone('0700000000'),
            $contactPoint
        );
    }

    /** Verify that no contact point is mapped without ACF fields. */
    public function testReturnsNullWhenAcfIsMissing(): void
    {
        $data        = json_decode(file_get_contents(__DIR__ . '/demand.data.json'), true, 512, JSON_THROW_ON_ERROR)[0];
        $data['acf'] = null;

        self::assertNull((new MapContactPoint())->map($data));
    }
}
