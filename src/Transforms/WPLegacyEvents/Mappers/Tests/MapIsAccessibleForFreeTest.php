<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapIsAccessibleForFree;

#[CoversClass(MapIsAccessibleForFree::class)]
final class MapIsAccessibleForFreeTest extends TestCase
{
    #[TestDox('event::isAccessibleForFree is true when prices are null or zero')]
    public function testHandlesZeroOrNullPrices()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123,
                "price_adult": "0",
                "price_children": "0",
                "children_age": null,
                "price_student": null
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }
    #[TestDox('event::isAccessibleForFree is true when prices are missing')]
    public function testHandlesMissingPrices()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }
    #[TestDox('event::isAccessibleForFree is false when price_adult is greater than zero')]
    public function testHandlePriceAdultGreaterThanZero()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123,
                "price_adult": "100"
            }',
            Schema::event()->isAccessibleForFree(false)
        );
    }
    #[TestDox('event::isAccessibleForFree is false when price_children is greater than zero')]
    public function testHandlePriceChildrenGreaterThanZero()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123,
                "price_children": 100
            }',
            Schema::event()->isAccessibleForFree(false)
        );
    }
    #[TestDox('event::isAccessibleForFree is false when price_student is greater than zero')]
    public function testHandlePriceStudentGreaterThanZero()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123,
                "price_student": "100"
            }',
            Schema::event()->isAccessibleForFree(false)
        );
    }
    #[TestDox('event::isAccessibleForFree is false when price_senior is greater than zero')]
    public function testHandlePriceSeniorGreaterThanZero()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123,
                "price_senior": "100"
            }',
            Schema::event()->isAccessibleForFree(false)
        );
    }
}
