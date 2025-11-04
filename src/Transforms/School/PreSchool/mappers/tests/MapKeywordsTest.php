<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('preschool::keywords is taken from _embedded->acf:term with taxonomy usp')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Pingisbord",
                            "taxonomy": "usp"
                        },
                        {},
                        null,
                        {
                            "name": "Will be skipped since area taxonomy is blacklisted",
                            "taxonomy": "area"
                        },
                        {
                            "name": "Hoppborg",
                            "taxonomy": "usp"
                        }
                    ]
                }
            }',
            Schema::preschool()
                ->keywords([
                    Schema::definedTerm()
                        ->name('Pingisbord')
                        ->description('Pingisbord')
                        ->inDefinedTermSet('usp'),
                    Schema::definedTerm()
                        ->name('Hoppborg')
                        ->description('Hoppborg')
                        ->inDefinedTermSet('usp')
                ])
        );
    }
}
