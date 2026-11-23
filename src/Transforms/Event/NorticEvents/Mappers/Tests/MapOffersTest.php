<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapOffers;

#[CoversClass(MapOffers::class)]
final class MapOffersTest extends TestCase
{
    #[TestDox('event::offers are mapped from source show prices and links')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{"shows":[{"link":"https://example.com/show/1","minPrice":120,"maxPrice":150}]}',
            Schema::event()->offers([
                Schema::offer()
                    ->url('https://example.com/show/1')
                    ->priceSpecification(
                        Schema::priceSpecification()
                            ->priceCurrency('SEK')
                            ->minPrice(120)
                            ->maxPrice(150)
                    )
            ])
        );
    }

    #[TestDox('event::offers is empty when shows have no prices')]
    public function testNoPrices(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{"shows":[{"link":"https://example.com/show/1"}]}',
            Schema::event()->offers([])
        );
    }
}
