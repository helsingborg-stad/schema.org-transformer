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
                    "title": "Ansök",
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
                ->title('Välj skola här')
                ->url('https://skolan.se')
                ->description('Ansök')
                ->disambiguatingDescription('Ansök till skolan via någon av nedan länkar'),
            Schema::action()
                ->name('cta_how_to_apply')
                ->title('Så här söker du')
                ->url('https://skolan.se/sa-har-soker-du')
                ->description('Ansök')
                ->disambiguatingDescription('Ansök till skolan via någon av nedan länkar')
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
}
