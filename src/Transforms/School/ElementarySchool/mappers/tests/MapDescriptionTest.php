<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('elementarySchool::description is taken from acf.description')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "acf": {
                    "custom_excerpt": "Detta är en beskrivning av skolan",
                    "visit_us": "Välkommen på besök",
                    "information": {
                        "": null,
                        "about_us": "redaktionell om oss",
                        "how_we_work": "redaktionell hur vi arbetar",
                        "optional": [
                        {
                            "heading": "extra rubrik 1",
                            "content": "extra innehåll 1"
                        },{
                            "heading": "extra rubrik 2",
                            "content": "extra innehåll 2"
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
        ',
            Schema::elementarySchool()
            ->description([
                Schema::textObject()->name("custom_excerpt")->text("Detta är en beskrivning av skolan"),
                Schema::textObject()->name("visit_us")->text("Välkommen på besök")->headline('Besök oss'),
                Schema::textObject()->name("about_us")->text("redaktionell om oss")->headline('Om oss'),
                Schema::textObject()->name("how_we_work")->text("redaktionell hur vi arbetar")->headline('Så arbetar vi'),
                Schema::textObject()->name("extra rubrik 1")->text("extra innehåll 1")->headline('extra rubrik 1'),
                Schema::textObject()->name("extra rubrik 2")->text("extra innehåll 2")->headline('extra rubrik 2'),
                Schema::textObject()->name("Sida 1")->text("Innehåll för sida 1")->headline('Sida 1'),
                Schema::textObject()->name("Sida 2")->text("Innehåll för sida 2")->headline('Sida 2')
            ])
        );
    }
}
