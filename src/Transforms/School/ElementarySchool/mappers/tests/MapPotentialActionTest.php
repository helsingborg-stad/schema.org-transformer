<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapPotentialAction;

#[CoversClass(MapPotentialAction::class)]
final class MapPotentialActionTest extends TestCase
{
    #[TestDox('elementarySchool::potentialAction is taken from acf.cta_application')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(),
            '{
                "acf": {
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
                    }
                }
            }',
            Schema::elementarySchool()
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
        );
    }

    #[TestDox('elementarySchool::potentialAction is acf.cta_application.* when url and title are both set')]
    public function testUrlAndTitlrMustBeSet(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(),
            '{
                "acf": {
                    "cta_application": {
                        "title": "Ansök",
                        "description": "Ansök till skolan via någon av nedan länkar",
                        "cta_missing_title": {
                            "url": "https://has-no-title.se"
                        },
                        "cta_missing_url": {
                            "title": "missing url"
                        },
                        "cta_good": {
                            "title": "Good one",
                            "url": "https://skolan.se"
                        }
                    }
                }
            }',
            Schema::elementarySchool()
                ->potentialAction([
                    Schema::action()
                        ->name('cta_good')
                        ->title('Good one')
                        ->url('https://skolan.se')
                        ->description('Ansök')
            ->disambiguatingDescription('Ansök till skolan via någon av nedan länkar')])
        );
    }

    #[TestDox('elementarySchool::potentialAction is [] when acf.cta_application.display_on_website is false')]
    public function testHideActions(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(),
            '{
                "acf": {
                    "cta_application": {
                        "display_on_website": false,
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
            }',
            Schema::elementarySchool()->potentialAction([])
        );
    }
}
