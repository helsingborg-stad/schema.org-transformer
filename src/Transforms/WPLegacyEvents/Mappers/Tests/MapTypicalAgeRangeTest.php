<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapTypicalAgeRange;

#[CoversClass(MapTypicalAgeRange::class)]
final class MapTypicalAgeRangeTest extends TestCase
{
    #[TestDox('event::typicalAgeRange is mapped from age_group_from and age_group_to')]
    public function testFromTo()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(),
            '{
                "age_group_from": "10",
                "age_group_to": "12"
            }',
            Schema::event()->typicalAgeRange('10-12')
        );
    }

     #[TestDox('event::typicalAgeRange is mapped from age_group_from')]
    public function testFrom()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(),
            '{
                "age_group_from": "10",
                "age_group_to": null
            }',
            Schema::event()->typicalAgeRange('10+')
        );
    }
     #[TestDox('event::typicalAgeRange is mapped from age_group_from')]
    public function testTo()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(),
            '{
                "age_group_from": null,
                "age_group_to": 18
            }',
            Schema::event()->typicalAgeRange('0-18')
        );
    }

    #[TestDox('event::typicalAgeRange(null) when age_group_from and age_group_to are missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(),
            '{"id": 123}',
            Schema::event()->typicalAgeRange(null)
        );
    }
}
