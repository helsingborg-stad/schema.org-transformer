<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\School\ElementarySchool\ElementarySchoolTransform;
use Municipio\Schema\Schema;

#[CoversClass(ElementarySchoolTransform::class)]
final class ElementarySchoolTransformTest extends TestCase
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
        $expectedSchool = Schema::elementarySchool()
        ->identifier("123")
        ->description([])
        ->keywords([])
        ->location([])
        ->event([])
        ->potentialAction([])
        ->areaServed([])
        ->image([])
        ->employee([])
        ->contactPoint([])
        ->hasOfferCatalog([])
        ->video([])
        ->setProperty('x-created-by', 'municipio://schema.org-transformer/elementary-school');

        $actualSchool = (new ElementarySchoolTransform())->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool
        );
    }

    #[TestDox('it works with typical source data')]
    public function testItWorks()
    {
        $source         = $this->prepareJsonForTransform('
        {
            "id": 123,
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
            ],
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
            ],
            "acf": {
                "link_facebook": "https://facebook.com/skolan",
                "link_instagram": "https://instagram.com/skolan",
                "video": "https://skolan.se/video.mp4",
                "number_of_students": "350",
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
                },
                "visiting_address": [{
                    "address": {
                        "name": "Testskolan",
                        "address": "Testskolan, Skolgatan 1",
                        "lat": 1.234,
                        "lng": 5.678
                    }
                }],
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
                },
                "open_hours_leisure_center": {
                    "open": "06:00:00",
                    "close": "18:00:00"
                }
            },
            "pages_embedded": [
                {
                    "post_title": "Sida 1",
                    "post_content": "Innehåll för sida 1"
                },
                {
                    "post_title": "Sida 2",
                    "post_content": "Innehåll för sida 2"
                }
            ],
            "_embedded": {
                "acf:term": [
                    {
                        "name": "Pingisbord",
                        "taxonomy": "usp"
                    },
                    {},
                    null,
                    {
                        "name": "Område A",
                        "taxonomy": "area"
                    },
                    {
                        "name": "Hoppborg",
                        "taxonomy": "usp"
                    },
                    {
                        "name": "Område B",
                        "taxonomy": "area"
                    },
                    {
                        "name": "Anpassad skola",
                        "taxonomy": "grade"
                    },
                    {
                        "name": "Årskurs F-9",
                        "taxonomy": "grade"
                    }
                ]
            }
        }');
        $expectedSchool = Schema::elementarySchool()
        ->identifier("123")
        ->description([
            Schema::textObject()->name("custom_excerpt")->text("Detta är en beskrivning av skolan"),
            Schema::textObject()->name("about_us")->text("redaktionell om oss")->headline('Om oss'),
            Schema::textObject()->name("how_we_work")->text("redaktionell hur vi arbetar")->headline('Så arbetar vi'),
            Schema::textObject()->name("extra rubrik")->text("extra innehåll")->headline('extra rubrik'),
            Schema::textObject()->name("Sida 1")->text("Innehåll för sida 1")->headline('Sida 1'),
            Schema::textObject()->name("Sida 2")->text("Innehåll för sida 2")->headline('Sida 2')
        ])
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
        ->location([
            Schema::place()
                ->name("Testskolan")
                ->address("Testskolan, Skolgatan 1")
                ->latitude(1.234)
                ->longitude(5.678)
        ])
        ->event([])
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
            ])
        ->areaServed(['Område A', 'Område B'])
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
            ])
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
        ])
        ->afterSchoolCare(Schema::service()
            ->name('Fritidsverksamhet')
            ->description('Öppettider för fritidsverksamhet')
            ->hoursAvailable(
                Schema::openingHoursSpecification()
                    ->opens("06:00:00")
                    ->closes("18:00:00")
            ))
        ->contactPoint([
            Schema::contactPoint()
                ->name('facebook')
                ->contactType('socialmedia')
                ->url('https://facebook.com/skolan'),
            Schema::contactPoint()
                ->name('instagram')
                ->contactType('socialmedia')
                ->url('https://instagram.com/skolan')
        ])
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
        ->numberOfStudents(350)
        ->video([
            Schema::videoObject()
                ->url('https://skolan.se/video.mp4')
            ])
        ->setProperty('x-created-by', 'municipio://schema.org-transformer/elementary-school');

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
                },
                "pages_embedded": [
                    {
                        "post_title": "Sida 1",
                        "post_content": "Innehåll för sida 1"
                    },
                    {
                        "post_title": "Sida 2",
                        "post_content": "Innehåll för sida 2"
                    }
                ]
            }
        ');
        $expectedSchool = Schema::elementarySchool()
        ->description([
            Schema::textObject()->name("custom_excerpt")->text("Detta är en beskrivning av skolan"),
            Schema::textObject()->name("about_us")->text("redaktionell om oss")->headline('Om oss'),
            Schema::textObject()->name("how_we_work")->text("redaktionell hur vi arbetar")->headline('Så arbetar vi'),
            Schema::textObject()->name("extra rubrik")->text("extra innehåll")->headline('extra rubrik'),
            Schema::textObject()->name("Sida 1")->text("Innehåll för sida 1")->headline('Sida 1'),
            Schema::textObject()->name("Sida 2")->text("Innehåll för sida 2")->headline('Sida 2')
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
                            "name": "Will be skipped since area taxonomy is blacklisted",
                            "taxonomy": "area"
                        },
                        {
                            "name": "Will be skipped since grade taxonomy is blacklisted",
                            "taxonomy": "grade"
                        },
                        {
                            "name": "Hoppborg",
                            "taxonomy": "usp"
                        }
                    ]
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
            ->name('Hoppborg')
            ->description('Hoppborg')
            ->inDefinedTermSet('usp')
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
        ->location([
            Schema::place()
                ->name("Testskolan")
                ->address("Testskolan, Skolgatan 1")
                ->latitude(1.234)
                ->longitude(5.678)
        ]);

        $actualSchool = (new ElementarySchoolTransform())->transformPlace(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('place and location attributes does not override elementary school name')]
    public function testTransformPlaceDoesNotOverrideName()
    {
        $source = [
            'acf' => [
                'visiting_address' => [[
                    'address' => [
                        'name'    => 'Testskolan',
                        'address' => 'Testskolan, Skolgatan 1',
                        'lat'     => 1.234,
                        'lng'     => 5.678
                    ]
                ]]
            ]
        ];

        $actualSchool = (new ElementarySchoolTransform())->transformPlace(
            Schema::elementarySchool()
                ->name("Bästa skolan"),
            $source
        );

        $this->assertEquals('Bästa skolan', $actualSchool->getProperty('name'));
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

        $expectedSchool = Schema::elementarySchool()
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

        $actualSchool = (new ElementarySchoolTransform())->transformImages(
            Schema::elementarySchool(),
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
        $expectedSchool = Schema::elementarySchool()
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
        $actualSchool   = (new ElementarySchoolTransform())->transformEmployees(
            Schema::elementarySchool(),
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

        $expectedSchool = Schema::elementarySchool()
        ->numberOfStudents(350);

        $actualSchool = (new ElementarySchoolTransform())->transformNumberOfStudents(
            Schema::elementarySchool(),
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

        $expectedSchool = Schema::elementarySchool()
        ->afterSchoolCare(Schema::service()
            ->name('Fritidsverksamhet')
            ->description('Öppettider för fritidsverksamhet')
            ->hoursAvailable(
                Schema::openingHoursSpecification()
                    ->opens("06:00:00")
                    ->closes("18:00:00")
            ));

        $actualSchool = (new ElementarySchoolTransform())->transformAfterSchoolCareHours(
            Schema::elementarySchool(),
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

        $expectedSchool = Schema::elementarySchool()
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

        $actualSchool = (new ElementarySchoolTransform())->transformContactPoint(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies hasOfferCatalog from acf:term with taxonomy grade')]
    public function testTransformHasOfferCatalog()
    {
        $source         = $this->prepareJsonForTransform('
            {
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
            }
        ');
        $expectedSchool = Schema::elementarySchool()
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
                ]);

        $actualSchool = (new ElementarySchoolTransform())->transformHasOfferCatalog(
            Schema::elementarySchool(),
            $source
        );

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }

    #[TestDox('applies acf.video as video')]
    public function testTransformVideo()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf":
                    {
                        "video": "https://skolan.se/video.mp4"
                    }
            }');
        $expectedSchool = Schema::elementarySchool()
        ->video([
        Schema::videoObject()
        ->url('https://skolan.se/video.mp4')
        ]);
        $actualSchool   = (new ElementarySchoolTransform())->transformVideo(
            Schema::elementarySchool(),
            $source
        );
        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }
}
