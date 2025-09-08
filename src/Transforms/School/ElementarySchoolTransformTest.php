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

    #[TestDox('description is mined from acf.information')]
    public function testTransformDescription()
    {
        $source         = $this->prepareJsonForTransform('
            {
                "acf": {
                    "custom_excerpt": "Detta är en beskrivning av skolan",
                    "information": {
                        "": null,
                        "about_us": "om oss",
                        "how_we_work": "hur vi arbetar",
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
                    Schema::textObject()->name("about_us")->text("om oss"),
                    Schema::textObject()->name("how_we_work")->text("hur vi arbetar"),
                    Schema::textObject()->name("extra rubrik")->text("extra innehåll")
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
        // TODO: Mock Typesense client and test that events are applied correctly
        // For now, just ensure the method can be called without error
        $this->assertTrue(true);

        $source         = $this->prepareJsonForTransform('
            {
                "acf": {}
            }
        ');
        $expectedSchool = Schema::elementarySchool();
        $actualSchool   = (new ElementarySchoolTransform())->transformEvents(
            Schema::elementarySchool(),
            $source
        );
        $this->assertEquals(
            $expectedSchool->toArray(),
            $actualSchool->toArray()
        );
    }
}
