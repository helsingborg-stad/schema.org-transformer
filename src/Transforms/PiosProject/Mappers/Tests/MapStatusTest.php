<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapStatus;

#[CoversClass(MapStatus::class)]
final class MapStatusTest extends TestCase
{
    #[TestDox('project::status is taken from projectStatus and projectPhase')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStatus(),
            '{
                "projectStatus": "One",
                "projectPhase": "Started"
            }',
            Schema::project()->status(
                Schema::progressStatus()
                    ->minNumber(0)
                    ->maxNumber(100)
                    ->name('Pågående')
                    ->number(33)
            )
        );
    }

    #[TestDox('project::status is defaulted if projectStatus and projectPhase are missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStatus(),
            '{"id": 123}',
            Schema::project()->status(
                Schema::progressStatus()
                    ->minNumber(0)
                    ->maxNumber(100)
                    ->name('Ingen status')
                    ->number(0)
            )
        );
    }
}
