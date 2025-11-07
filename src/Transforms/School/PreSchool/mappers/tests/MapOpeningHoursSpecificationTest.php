<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapOpeningHoursSpecification;

#[CoversClass(MapOpeningHoursSpecification::class)]
final class MapOpeningHoursSpecificationTest extends TestCase
{
    #[TestDox('preschool::openingHoursSpecification is taken from acf.open_hours when both open and close are set')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOpeningHoursSpecification(),
            '{
                "acf":
                    {
                        "open_hours": {
                            "open": "08:00",
                            "close": "16:00"
                        }
                    }
            }',
            Schema::preschool()
                ->openingHoursSpecification(
                    Schema::openingHoursSpecification()
                        ->opens('08:00')
                        ->closes('16:00')
                )
        );
    }

    #[TestDox('preschool::openingHoursSpecification is not set when open or close is missing')]
    public function testItSkipsWhenDataIsMissing(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOpeningHoursSpecification(),
            '{
                "acf":
                    {
                        "open_hours": {
                            "open": null,
                            "close": "16:00"
                        }
                    }
            }',
            Schema::preschool()
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOpeningHoursSpecification(),
            '{
                "acf":
                    {
                        "open_hours": {
                            "open": "08:00",
                            "close": null
                        }
                    }
            }',
            Schema::preschool()
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOpeningHoursSpecification(),
            '{
                "acf":
                    {
                        "open_hours": {
                            "open": null,
                            "close": null
                        }
                    }
            }',
            Schema::preschool()
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOpeningHoursSpecification(),
            '{ "id": 123 }',
            Schema::preschool()
        );
    }
}
