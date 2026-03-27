<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEndDate::class)]
final class MapEndDateTest extends TestCase
{
    #[TestDox('event::endDate is taken from $.endDate')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{
                "endDate": "2024-06-01T12:00:00Z"
            }',
            Schema::event()->endDate('2024-06-01T12:00:00Z')
        );
    }

    #[TestDox('event::endDate(null) when $.endDate is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{
                "id": 123
            }',
            Schema::event()->endDate(null)
        );
    }
}
