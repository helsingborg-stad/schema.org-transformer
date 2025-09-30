<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('event::description() is taken from content.rendered')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "content": {
                    "rendered": "<div>Vi erbjuder planerade aktiviteter för Årskurs 4-6 i nya idrotthallen.</div>"
                }
            }',
            Schema::event()->description(['<div>Vi erbjuder planerade aktiviteter för Årskurs 4-6 i nya idrotthallen.</div>'])
        );
    }

    #[TestDox('event::description(null) when content is missing')]
    public function testHandlesMissingContent()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{"id": 123}',
            Schema::event()->description([])
        );
    }
}
