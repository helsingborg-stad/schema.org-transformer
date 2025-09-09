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

    // #[TestDox('its doesnt break when a lot is missing')]
    // public function testAlmostNoSourceData()
    // {
    //     $source         = $this->prepareJsonForTransform('{
    //         "id": 123
    //     }');
    //     $expectedSchool = Schema::elementarySchool();

    //     $actualSchool = (new ElementarySchoolTransform())->transform(
    //         [$source]
    //     )[0];

    //     $this->assertEquals(
    //         $expectedSchool->toArray(),
    //         $actualSchool->toArray()
    //     );
    // }

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
                            "name": "Pingisbord"
                        },
                        {
                            "name": "Bibliotek"
                        }]
                }
            }
        ');
        $expectedSchool = Schema::elementarySchool()
            ->keywords(['Pingisbord', 'Bibliotek']);

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
}
