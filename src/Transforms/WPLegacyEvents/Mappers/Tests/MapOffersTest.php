<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapOffers;

#[CoversClass(MapOffers::class)]
final class MapOffersTest extends TestCase
{
    #[TestDox('event::offers is mapped from offers')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "price_adult": "250",
                "price_children": "125",
                "price_student": "175",
                "price_senior": "200",
                "price_range": {
                    "seated_minimum_price": "20",
                    "seated_maximum_price": "40",
                    "standing_minimum_price": "30",
                    "standing_maximum_price": "60"
                }
            }',
            Schema::event()->offers([
                Schema::offer()
                    ->price('250')
                    ->priceCurrency('SEK')
                    ->name('Standard/Vuxen'),
                Schema::offer()
                    ->price('125')
                    ->priceCurrency('SEK')
                    ->name('Barn'),
                Schema::offer()
                    ->price('175')
                    ->priceCurrency('SEK')
                    ->name('Student'),
                Schema::offer()
                    ->price('200')
                    ->priceCurrency('SEK')
                    ->name('Pensionär'),
                Schema::offer()
                    ->priceCurrency('SEK')
                    ->name('Sittplats')
                    ->priceSpecification(Schema::priceSpecification()
                        ->minPrice('20')
                        ->maxPrice('40')),
                Schema::offer()
                    ->priceCurrency('SEK')
                    ->name('Ståplats')
                    ->priceSpecification(Schema::priceSpecification()
                        ->minPrice('30')
                        ->maxPrice('60')),
            ])
        );
    }

    #[TestDox('event::offers with seated price range only')]
    public function testOnlySeatedRange()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "price_range": {
                    "seated_minimum_price": "20",
                    "seated_maximum_price": "40"
                }
            }',
            Schema::event()->offers([
                Schema::offer()
                    ->priceCurrency('SEK')
                    ->name('Sittplats')
                    ->priceSpecification(Schema::priceSpecification()
                        ->minPrice('20')
                        ->maxPrice('40')),
            ])
        );
    }

    #[TestDox('event::offers with senior price only')]
    public function testOnlySenior()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "price_senior": "200"
            }',
            Schema::event()->offers([
                Schema::offer()
                    ->price('200')
                    ->priceCurrency('SEK')
                    ->name('Pensionär'),
            ])
        );
    }

    #[TestDox('event::offers is not mapped when no offers are available')]
    public function testItDoesNotMapWhenNoOffers()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{"id": 123}',
            Schema::event()->offers([])
        );
    }
}
