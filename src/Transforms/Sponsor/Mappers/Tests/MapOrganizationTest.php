<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOrganization;

#[CoversClass(MapOrganization::class)]
final class MapOrganizationTest extends TestCase
{
    /** Verify that demand organization metadata is mapped to an organization. */
    public function testMapsDemandOrganization(): void
    {
        $data = TestHelper::fixture('demand.data.json');

        $organization = (new MapOrganization())->map($data);

        self::assertEquals(
            Schema::organization()
                ->name('Exempelföreningen')
                ->description('Beskrivning av exempelföreningen')
                ->url(null)
                ->taxID('0000000000'),
            $organization
        );
    }

    /** Verify that missing organization fields do not create invented values. */
    public function testMapsMissingOrganizationFieldsAsNull(): void
    {
        $data        = TestHelper::fixture('demand.data.json');
        $data['acf'] = [];

        self::assertEquals(
            Schema::organization()->name(null)->description(null)->url(null)->taxID(null),
            (new MapOrganization())->map($data)
        );
    }
}
