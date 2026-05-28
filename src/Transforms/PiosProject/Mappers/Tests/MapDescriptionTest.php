<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('project::description is taken from description, benefitsAndEffects, goals and customDimensions')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "description": "Test description",
                "benefitsAndEffects": "Test benefits and effects",
                "goals": [{"name": "Goal 1"}, {"name": "Goal 2"}, {"name": null}, {}, null],
                "risks": [{"description": "Risk 1"}, {"description": "Risk 2"}, {"description": null}, {}, null],
                "customDimensions": [
                    {
                        "name": "Beskrivning av status",
                        "value": "En statusbeskrivning av projektet.",
                        "values": null
                    },
                    {
                        "name": "Prioriterade områden VP",
                        "values": ["Område 1", "Område 2"]
                    }
                ]
            }',
            Schema::project()->description([
                Schema::textObject()->text("Test description")->name('description'),
                Schema::textObject()->text("Test benefits and effects")->headline('<h2>Nyttor och effekter</h2>')->name('benefitsAndEffects'),
                Schema::textObject()->text("<ul><li>Goal 1</li><li>Goal 2</li></ul>")->headline('<h2>Mål</h2>')->name('goals'),

                Schema::textObject()->text("En statusbeskrivning av projektet.")->headline('<h2>Beskrivning av status</h2>')->name(null),
                Schema::textObject()->text("<ul><li>Område 1</li><li>Område 2</li></ul>")->headline('<h2>Prioriterade områden VP</h2>')->name(null),
                // Schema::textObject()->text("<ul><li>Risk 1</li><li>Risk 2</li></ul>")->headline('<h2>Risker</h2>')->name('risks')
            ])
        );
    }

    #[TestDox('project::description([]) if no data is present')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{"id": 123}',
            Schema::project()->description([])
        );
    }
}
