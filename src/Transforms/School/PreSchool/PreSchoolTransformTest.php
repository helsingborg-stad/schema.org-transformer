<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\School\PreSchool\PreSchoolTransform;
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
        ->location([])
        ->event([])
        ->potentialAction([])
        ->areaServed([])
        ->image([])
        ->employee([])
        ->contactPoint([])
        ->video([])
        ->setProperty('x-created-by', 'municipio://schema.org-transformer/pre-school');

        $actualSchool = (new PreSchoolTransform())->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool
        );
    }

    public function testItWorks()
    {
        $source = $this->prepareJsonForTransform('{
            "id": 123,
            "images": [
                {
                    "ID": 1,
                    "title": "Bildtitel 1",
                    "caption": "Bildtext 1",
                    "alt": "Alternativ text 1",
                    "url": "https://skolan.se/image1.jpg"
                }],
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
                "video": "https://youtu.be/dQw4w9WgXcQ",
                "custom_excerpt": "Detta är en beskrivning av skolan",
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
                }
            },
            "_embedded": {
                "acf:term": [{
                    "name": "Ett nycklord",
                    "taxonomy": "usp"
                }, {
                    "name": "Område A",
                    "taxonomy": "area"
                }]
            }
        }');

        $expectedSchool = Schema::preschool()
            ->identifier("123")
            ->description([
                    Schema::textObject()->name("custom_excerpt")->headline('')->text(
                        "Detta är en beskrivning av skolan"
                    )])
        ->keywords([Schema::definedTerm()
                        ->name('Ett nycklord')
                        ->description('Ett nycklord')
                        ->inDefinedTermSet('usp')])
        ->location([
            Schema::place()->name("Testskolan")
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
        ->areaServed(['Område A'])
        ->image([
            Schema::imageObject()
                ->name('Bildtitel 1')
                ->caption('Bildtext 1')
                ->description('Alternativ text 1')
                ->url('https://skolan.se/image1.jpg')])
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
        ->video([
                Schema::videoObject()
                    ->url('https://youtu.be/dQw4w9WgXcQ')
            ])
        ->setProperty('x-created-by', 'municipio://schema.org-transformer/pre-school');

        $actualSchool = (new PreSchoolTransform())->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool
        );
    }
}
