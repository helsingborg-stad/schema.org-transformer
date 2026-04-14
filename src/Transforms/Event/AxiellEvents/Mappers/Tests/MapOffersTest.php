<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapOffers::class)]
final class MapOffersTest extends TestCase
{
    #[TestDox('event::offers([]) is hardcoded')]
    public function testItsHardCoded()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "id": 123
            }',
            Schema::event()->offers([])
        );
    }
}
