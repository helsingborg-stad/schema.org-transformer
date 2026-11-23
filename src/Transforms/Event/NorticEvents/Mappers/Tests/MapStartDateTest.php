<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapStartDate;

#[CoversClass(MapStartDate::class)]
final class MapStartDateTest extends TestCase
{
    #[TestDox('event::startDate is the earliest source shows->startDate')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{"shows":[{"startDate":"2026-11-28 15:00"},{"startDate":"2026-11-27 19:00"}]}',
            Schema::event()->startDate('2026-11-27 19:00')
        );
    }

    #[TestDox('event::startDate is null when no show has a startDate')]
    public function testNoDates(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{"shows":[]}',
            Schema::event()->startDate(null)
        );
    }
}
