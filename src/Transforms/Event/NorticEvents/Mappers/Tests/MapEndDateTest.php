<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEndDate;

#[CoversClass(MapEndDate::class)]
final class MapEndDateTest extends TestCase
{
    #[TestDox('event::endDate is the latest source shows->startDate')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{"shows":[{"startDate":"2026-11-27 19:00"},{"startDate":"2026-11-29 15:00"}]}',
            Schema::event()->endDate('2026-11-29 15:00')
        );
    }

    #[TestDox('event::endDate is null when no show has a startDate')]
    public function testNoDates(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{"shows":[]}',
            Schema::event()->endDate(null)
        );
    }
}
