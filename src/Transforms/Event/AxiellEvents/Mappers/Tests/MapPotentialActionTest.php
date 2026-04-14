<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapPotentialAction::class)]
final class MapPotentialActionTest extends TestCase
{
    #[TestDox('event::potentialAction([]) is hardcoded')]
    public function testItsHardcoded()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(),
            '{
                "id": 123
            }',
            Schema::event()->potentialAction([])
        );
    }
}
