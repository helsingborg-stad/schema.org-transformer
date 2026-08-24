<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapFunding;

#[CoversClass(MapFunding::class)]
final class MapFundingTest extends TestCase
{
    #[TestDox('project::funding is taken from funding')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapFunding(),
            '{
                "periods": [{
                        "year": 2023,
                        "_": "no budget, skipped"
                    },
                    {
                        "year": 2024,
                        "totalBudget": 1000000
                    },
                    {
                        "year": 2025,
                        "totalBudget": 2000000
                    },
                    {
                        "totalBudget": 3000000
                    }
                ]
            }',
            Schema::project()->funding([
                Schema::monetaryGrant()->amount(1000000)->name('2024'),
                Schema::monetaryGrant()->amount(2000000)->name('2025'),
                Schema::monetaryGrant()->amount(3000000)
            ])
        );
    }

    #[TestDox('project::funding is empty if periods is empty')]
    public function testEmpty()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapFunding(),
            '{
                "periods": []
            }',
            Schema::project()->funding([])
        );
    }

    #[TestDox('project::funding is empty if periods is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapFunding(),
            '{
                "id": 123
            }',
            Schema::project()->funding([])
        );
    }
}
