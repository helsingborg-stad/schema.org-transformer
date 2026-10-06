<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapDemand;

#[CoversClass(MapDemand::class)]
final class MapDemandTest extends TestCase
{
    /** Verify that demand requirements become the description. */
    public function testMapsRequirements(): void
    {
        $data = TestHelper::fixture('demand.data.json');

        $demand = (new MapDemand())->map($data);

        self::assertEquals(Schema::demand()->description('Test krav'), $demand);
    }

    /** Verify that missing requirements do not supply a description. */
    public function testMapsMissingRequirementsAsNull(): void
    {
        $data = TestHelper::fixture('demand.data.json');
        unset($data['acf']['requirements']);

        self::assertEquals(Schema::demand()->description(null), (new MapDemand())->map($data));
    }
}
