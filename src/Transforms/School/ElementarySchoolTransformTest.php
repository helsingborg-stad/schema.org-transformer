<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\ElementarySchoolTransform;
use Municipio\Schema\Schema;

#[CoversClass(ElementarySchoolTransform::class)]
final class ElementarySchoolTransformTest extends TestCase
{
    private function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    #[TestDox('its doesnt break when a lot is missing')]
    public function testAlmostNoSourceData()
    {
        $source         = $this->prepareJsonForTransform('{
            "id": 123
        }');
        $expectedSchool = Schema::elementarySchool()
            ->identifier("123")
            ->additionalProperty([])
            ->description([])
            ->keywords([])
            ->event([])
            ->potentialAction([])
            ->areaServed([]);

        $actualSchool = (new ElementarySchoolTransform())->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool
        );
    }

    #[TestDox('description is mined from acf.information')]
    public function testTransformDescription()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf": {
                    "custom_excerpt": "Detta är en beskrivning av skolan",
                    "information": {
                        "": null,
                        "about_us": "redaktionell om oss",
                        "how_we_work": "redaktionell hur vi arbetar",
                        "optional": [
                        {
                            "heading": "extra rubrik",
                            "content": "extra innehåll"
                        }]
                    }
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
                ->description([
                    "Detta är en beskrivning av skolan",
                    Schema::textObject()->name("about_us")->text("redaktionell om oss")->headline('Om oss'),
                    Schema::textObject()->name("how_we_work")->text("redaktionell hur vi arbetar")->headline('Hur vi arbetar'),
                    Schema::textObject()->name("extra rubrik")->text("extra innehåll")->headline('extra rubrik')
                ]);

        $actualSchool = (new ElementarySchoolTransform())->transformDescription(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies keywords')]
    public function testTransformKeywords()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Pingisbord",
                            "taxonomy": "usp"
                        },
                        {},
                        null,
                        {
                            "name": "Innerstad",
                            "taxonomy": "area"
                        }]
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->keywords([
                Schema::definedTerm()
                    ->name('Pingisbord')
                    ->description('Pingisbord')
                    ->inDefinedTermSet('usp'),
                Schema::definedTerm()
                    ->name('Innerstad')
                    ->description('Innerstad')
                    ->inDefinedTermSet('area')
                ]);

        $actualSchool = (new ElementarySchoolTransform())->transformKeywords(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies place and location attributes')]
    public function testTransformPlace()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf": {
                    "visiting_address": [{
                        "address": {
                            "name": "Testskolan",
                            "address": "Testskolan, Skolgatan 1",
                            "lat": 1.234,
                            "lng": 5.678
                        }
                    }]
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->location(
                Schema::place()
                    ->name("Testskolan")
                    ->address("Testskolan, Skolgatan 1")
                    ->latitude(1.234)
                    ->longitude(5.678)
            )
            // Place properties
            ->name("Testskolan")
            ->address("Testskolan, Skolgatan 1")
            ->latitude(1.234)
            ->longitude(5.678);

        $actualSchool = (new ElementarySchoolTransform())->transformPlace(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies events from typesense')]
    public function testTransformEvents()
    {
        $source                = $this->prepareJsonForTransform('
            {
                "acf": {}
            }
        ');
        $mockEventSearchClient = new class implements \SchemaTransformer\Transforms\School\Events\EventsSearchClient {
            public function searchEventsBySchoolName(string $schoolName): array
            {
                return [
                    [
                        '@context' => [
                            'schema'    => 'https://schema.org',
                            'municipio' => 'https://schema.municipio.tech/schema.jsonld'
                        ],
                        '@type'    => 'Event',
                        'name'     => 'Skolfest',
                    ],
                    [
                        '@context' => [
                            'schema'    => 'https://schema.org',
                            'municipio' => 'https://schema.municipio.tech/schema.jsonld'
                        ],
                        '@type'    => 'Event',
                        'name'     => 'Idrottsdag',
                    ],
                ];
            }
        };

        $expectedSchool = Schema::elementarySchool()->event([
            Schema::event()->name('Skolfest')->toArray(),
            Schema::event()->name('Idrottsdag')->toArray(),
        ]);
        $actualSchool   = (new ElementarySchoolTransform())
            ->withEventSearchClient($mockEventSearchClient)
            ->transformEvents(
                Schema::elementarySchool(),
                $source
            );
        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies potentialActions from acf.cta_application')]
    public function testTransformActions()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf": {
                    "cta_application": {
                        "description": "Ansök till skolan via någon av nedan länkar",
                        "cta_apply_here": {
                            "title": "Välj skola här",
                            "url": "https://skolan.se"
                        },
                        "cta_how_to_apply": {
                            "title": "Så här söker du",
                            "url": "https://skolan.se/sa-har-soker-du"
                        }
                    }
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->potentialAction([
                Schema::action()
                    ->name('cta_apply_here')
                    ->description('Ansök till skolan via någon av nedan länkar')
                    ->title('Välj skola här')
                    ->url('https://skolan.se'),
                Schema::action()
                    ->name('cta_how_to_apply')
                    ->description('Ansök till skolan via någon av nedan länkar')
                    ->title('Så här söker du')
                    ->url('https://skolan.se/sa-har-soker-du')
            ]);

        $actualSchool = (new ElementarySchoolTransform())->transformActions(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies areaServed from acf:term with taxonomy area')]
    public function testTransformAreaServed()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Område A",
                            "taxonomy": "area"
                        },
                        {
                            "name": "x",
                            "taxonomy": "y"
                        },
                        {
                            "name": "Område B",
                            "taxonomy": "area"
                        }
                    ]
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->areaServed(['Område A', 'Område B']);

        $actualSchool = (new ElementarySchoolTransform())->transformAreaServed(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies number of students and grades as additional properties')]
    public function testTransformAdditionalProperties()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf": {
                    "number_of_students": "350",
                    "_embedded": {
                        "acf:term": [
                            {
                                "name": "ettan",
                                "taxonomy": "grade"
                            },
                            {
                                "name": "tvåan",
                                "taxonomy": "grade"
                            },
                            {
                                "name": "x",
                                "taxonomy": "y"
                            }
                        ]
                    }
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->additionalProperty([
                'number_of_students' => 350,
                'grades'             => ['ettan', 'tvåan']
            ]);

        $actualSchool = (new ElementarySchoolTransform())->transformAdditionalProperties(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }
}
