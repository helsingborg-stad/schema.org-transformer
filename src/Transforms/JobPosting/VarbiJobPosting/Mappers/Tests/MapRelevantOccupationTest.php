<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapRelevantOccupation;

#[CoversClass(MapRelevantOccupation::class)]
final class MapRelevantOccupationTest extends TestCase
{
    #[TestDox('jobPosting::relevantOccupation is taken from the matching taxonomy')]
    public function testMapsMatchingTaxonomyNameToRelevantOccupation(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapRelevantOccupation(),
            '{
                "data": {
                    "relationships": {
                        "taxonomy": {
                            "data": {
                                "id": "taxonomy-123",
                                "type": "taxonomy"
                            }
                        }
                    }
                },
                "included": [
                    {
                        "id": "taxonomy-123",
                        "type": "taxonomy",
                        "attributes": {
                            "name": "Slagruteingenjör"
                        }
                    }
                ]
            }',
            Schema::jobPosting()->mapRelevantOccupation('Slagruteingenjör')
        );
    }

    #[TestDox('jobPosting::relevantOccupation is null when the taxonomy is not included')]
    public function testMapsMissingIncludedTaxonomyToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapRelevantOccupation(),
            '{
                "data": {
                    "relationships": {
                        "taxonomy": {
                            "data": {
                                "id": "taxonomy-123",
                                "type": "taxonomy"
                            }
                        }
                    }
                },
                "included": []
            }',
            Schema::jobPosting()->mapRelevantOccupation(null)
        );
    }

    #[TestDox('jobPosting is unchanged when the taxonomy relationship is missing')]
    public function testLeavesJobPostingUnchangedWhenTaxonomyRelationshipIsMissing(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapRelevantOccupation(),
            '{
                "data": {
                    "relationships": {}
                }
            }',
            Schema::jobPosting()
        );
    }
}
