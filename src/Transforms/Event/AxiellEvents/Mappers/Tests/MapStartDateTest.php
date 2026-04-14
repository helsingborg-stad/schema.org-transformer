<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapStartDate::class)]
final class MapStartDateTest extends TestCase
{
    #[TestDox('event::startDate is taken from $.startDate')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{
                "startDate": "2024-06-01T12:00:00Z"
            }',
            Schema::event()->startDate('2024-06-01T12:00:00Z')
        );
    }

    #[TestDox('event::startDate(null) when $.startDate is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{
                "id": 123
            }',
            Schema::event()->startDate(null)
        );
    }
}
