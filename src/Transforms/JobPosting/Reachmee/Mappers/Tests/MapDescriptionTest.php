<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('jobPosting::description is single textObject array taken from description')]
    public function testMapsDescription(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "description": "A role with meaningful work."
            }',
            Schema::jobPosting()->description([
                Schema::textObject()->text('A role with meaningful work.')
            ])
        );
    }

    #[TestDox('jobPosting::description is [] when description is missing')]
    public function testMapsMissingDescriptionToEmptyArray(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->description([])
        );
    }
}
