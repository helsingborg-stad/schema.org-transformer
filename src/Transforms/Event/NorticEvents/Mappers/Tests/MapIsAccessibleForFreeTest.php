<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapIsAccessibleForFree;

#[CoversClass(MapIsAccessibleForFree::class)]
final class MapIsAccessibleForFreeTest extends TestCase
{
    #[TestDox('event::isAccessibleForFree is true when a show has no-cost tickets')]
    public function testFreeShow(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{"shows":[{"minPrice":0,"maxPrice":0}]}',
            Schema::event()->isAccessibleForFree(true)
        );
    }

    #[TestDox('event::isAccessibleForFree is false for paid shows or missing prices')]
    public function testPaidOrUnpricedShow(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{"shows":[{"minPrice":120,"maxPrice":150}]}',
            Schema::event()->isAccessibleForFree(false)
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{"shows":[]}',
            Schema::event()->isAccessibleForFree(false)
        );
    }
}
