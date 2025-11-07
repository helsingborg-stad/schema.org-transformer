<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapHasOfferCatalog;

#[CoversClass(MapHasOfferCatalog::class)]
final class MapHasOfferCatalogTest extends TestCase
{
    #[TestDox('elementarySchool::hasOfferCatalog is taken from from acf:term with taxonomy grade')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapHasOfferCatalog(),
            '{
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Anpassad skola",
                            "taxonomy": "grade"
                        },
                        {
                            "name": "Årskurs F-9",
                            "taxonomy": "grade"
                        },
                        {
                            "name": "x",
                            "taxonomy": "y"
                        }
                    ]
                }
            }',
            Schema::elementarySchool()
                ->hasOfferCatalog([
                    Schema::offerCatalog()
                        ->name('Årskurser')
                        ->description('Årskurser som skolan erbjuder')
                        ->itemListElement([
                            Schema::listItem()
                                ->name('Anpassad skola')
                                ->description('Anpassad skola'),
                            Schema::listItem()
                                ->name('Årskurs F-9')
                                ->description('Årskurs F-9')
                        ])
            ])
        );
    }
}
