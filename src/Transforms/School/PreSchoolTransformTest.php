<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\School\PreSchoolTransform;
use Municipio\Schema\Schema;

#[CoversClass(PreSchoolTransform::class)]
final class PreSchoolTransformTest extends TestCase
{
    private function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    #[TestDox('it doesn\'t break when a lot is missing')]
    public function testAlmostNoSourceData()
    {
        $source         = $this->prepareJsonForTransform('{
            "id": 123
        }');
        $expectedSchool = Schema::preschool()
        ->identifier("123")
        ->description([])
        ->keywords([])
        ->event([])
        ->potentialAction([])
        ->areaServed([])
        ->image([])
        ->employee([])
        ->contactPoint([]);

        $actualSchool = (new PreSchoolTransform())->transform(
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
        $expectedSchool = Schema::preschool()
        ->description([
            Schema::textObject()->name("custom_excerpt")->text("Detta är en beskrivning av skolan"),
            Schema::textObject()->name("about_us")->text("redaktionell om oss")->headline('Om oss'),
            Schema::textObject()->name("how_we_work")->text("redaktionell hur vi arbetar")->headline('Hur vi arbetar'),
            Schema::textObject()->name("extra rubrik")->text("extra innehåll")->headline('extra rubrik')
        ]);

        $actualSchool = (new PreSchoolTransform())->transformDescription(
            Schema::preschool(),
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
                            "name": "Will be skipped since area taxonomy is blacklisted",
                            "taxonomy": "area"
                        },
                        {
                            "name": "Hoppborg",
                            "taxonomy": "usp"
                        }
                    ]
                }
            }
        ');
        $expectedSchool = Schema::preschool()
        ->keywords([
        Schema::definedTerm()
            ->name('Pingisbord')
            ->description('Pingisbord')
            ->inDefinedTermSet('usp'),
        Schema::definedTerm()
            ->name('Hoppborg')
            ->description('Hoppborg')
            ->inDefinedTermSet('usp')
        ]);

        $actualSchool = (new PreSchoolTransform())->transformKeywords(
            Schema::preschool(),
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
        $expectedSchool = Schema::preschool()
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

        $actualSchool = (new PreSchoolTransform())->transformPlace(
            Schema::preschool(),
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

        $expectedSchool = Schema::preschool()->event([
        Schema::event()->name('Skolfest')->toArray(),
        Schema::event()->name('Idrottsdag')->toArray(),
        ]);
        $actualSchool   = (new PreSchoolTransform())
        ->withEventSearchClient($mockEventSearchClient)
        ->transformEvents(
            Schema::preschool(),
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
        $expectedSchool = Schema::preschool()
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

        $actualSchool = (new PreSchoolTransform())->transformActions(
            Schema::preSchool(),
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
        $expectedSchool = Schema::preSchool()
        ->areaServed(['Område A', 'Område B']);

        $actualSchool = (new PreSchoolTransform())->transformAreaServed(
            Schema::preSchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies featured media images')]
    public function testTransformImages()
    {
        $source = $this->prepareJsonForTransform('
            {
                "images": [
                    {
                        "ID": 1,
                        "title": "Bildtitel 1",
                        "caption": "Bildtext 1",
                        "alt": "Alternativ text 1",
                        "url": "https://skolan.se/image1.jpg"
                    },
                    {
                        "ID": 2,
                        "title": "Bildtitel 2",
                        "caption": "Bildtext 2",
                        "alt": "Alternativ text 2",
                        "url": "https://skolan.se/image2.jpg"
                    }
                ]
            }');

        $expectedSchool = Schema::preschool()
        ->image([
        Schema::imageObject()
            ->name('Bildtitel 1')
            ->caption('Bildtext 1')
            ->description('Alternativ text 1')
            ->url('https://skolan.se/image1.jpg'),
        Schema::imageObject()
            ->name('Bildtitel 2')
            ->caption('Bildtext 2')
            ->description('Alternativ text 2')
            ->url('https://skolan.se/image2.jpg')
        ]);

        $actualSchool = (new PreSchoolTransform())->transformImages(
            Schema::preschool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies employees from source')]
    public function testTransformEmployees()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "employee": [
                    {
                        "job_title": "Administrativ samordnare",
                        "name": "Test person",
                        "email": "test.person@example.com",
                        "telephone": "123-456789",
                        "image": {
                            "ID": 0,
                            "url": "https://skolan.se/testperson.jpg",
                            "alt": "alternativ text",
                            "name": "testperson.jpg",
                            "caption": "Porträtt av Test Person"
                        }
                    }
                ]
            }');
        $expectedSchool = Schema::preschool()
        ->employee([
            Schema::person()
                ->name('Test person')
                ->jobTitle('Administrativ samordnare')
                ->email('test.person@example.com')
                ->telephone('123-456789')
                ->image(Schema::imageObject()
                    ->name('testperson.jpg')
                    ->caption('Porträtt av Test Person')
                    ->description('alternativ text')
                    ->url('https://skolan.se/testperson.jpg'))
        ]);
        $actualSchool   = (new PreSchoolTransform())->transformEmployees(
            Schema::preschool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies number of students from acf.number_of_students')]
    public function testTransformNumberOfStudents()
    {
        $source = $this->prepareJsonForTransform('
            {
                "acf": {
                    "number_of_students": "350"
                }
            }
        ');

        $expectedSchool = Schema::preschool()
        ->numberOfStudents(350);

        $actualSchool = (new PreSchoolTransform())->transformNumberOfStudents(
            Schema::preschool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies after school care hours from open_hours_leisure_center')]
    public function testTransformAfterSchoolCareHours()
    {
        $source = $this->prepareJsonForTransform('
            {
                "acf":
                {
                    "open_hours_leisure_center": {
                        "open": "06:00:00",
                        "close": "18:00:00"
                    }
                }
        }
        ');

        $expectedSchool = Schema::preschool()
        ->afterSchoolCare(Schema::service()
            ->name('Fritidsverksamhet')
            ->description('Öppettider för fritidsverksamhet')
            ->hoursAvailable(
                Schema::openingHoursSpecification()
                    ->opens("06:00:00")
                    ->closes("18:00:00")
            ));

        $actualSchool = (new PreSchoolTransform())->transformAfterSchoolCareHours(
            Schema::preschool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    public function testTransformContactPoint()
    {
        $source = $this->prepareJsonForTransform('
            {
                "acf":
                    {
                        "link_facebook": "https://facebook.com/skolan",
                        "link_instagram": "https://instagram.com/skolan"
                    }
            }
        ');

        $expectedSchool = Schema::preschool()
        ->contactPoint([
            Schema::contactPoint()
                ->name('facebook')
                ->contactType('socialmedia')
                ->url('https://facebook.com/skolan'),
            Schema::contactPoint()
                ->name('instagram')
                ->contactType('socialmedia')
                ->url('https://instagram.com/skolan')
        ]);

        $actualSchool = (new PreSchoolTransform())->transformContactPoint(
            Schema::preschool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }
}
