<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapIsAccessibleForFree::class)]
final class MapIsAccessibleForFreeTest extends TestCase
{
    #[TestDox('event::isAccessibleForFree(true) when acf.priceList is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }

    #[TestDox('event::isAccessibleForFree(true) when acf.priceList is empty')]
    public function testEmpty()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "acf": {
                    "pricesList": []
                }
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }

    #[TestDox('event::isAccessibleForFree(true) when acf.priceList has lowest price of 0')]
    public function testZero()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "acf": {
                    "pricesList": [{
                        "price": 100
                    },{
                        "price": 0
                    }, {
                        "price": 200
                    }]
                }
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }

    #[TestDox('event::isAccessibleForFree(false) when acf.priceList has all costs')]
    public function testHasCosts()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "acf": {
                    "pricesList": [{
                        "price": 100
                    }, {
                        "price": 200
                    }]
                }
            }',
            Schema::event()->isAccessibleForFree(false)
        );
    }
}
