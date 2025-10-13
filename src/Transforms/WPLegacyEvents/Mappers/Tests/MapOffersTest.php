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
                "booking_link": "https://booking-link.org",
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
                    ->name('Standard/Vuxen')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Standard/Vuxen')
                        ->price('250')
                        ->minPrice('250')
                        ->maxPrice('250')
                        ->priceCurrency('SEK')]),
                Schema::offer()
                    ->name('Barn')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Barn')
                        ->price('125')
                        ->minPrice('125')
                        ->maxPrice('125')
                        ->priceCurrency('SEK')]),
                Schema::offer()
                    ->name('Student')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Student')
                        ->price('175')
                        ->minPrice('175')
                        ->maxPrice('175')
                        ->priceCurrency('SEK')]),
                Schema::offer()
                    ->name('Pensionär')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Pensionär')
                        ->price('200')
                        ->minPrice('200')
                        ->maxPrice('200')
                        ->priceCurrency('SEK')]),
                Schema::offer()
                    ->name('Sittplats')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Sittplats')
                        ->minPrice('20')
                        ->maxPrice('40')])
                        ->priceCurrency('SEK'),
                Schema::offer()
                    ->name('Ståplats')
                    ->url('https://booking-link.org')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Ståplats')
                        ->minPrice('30')
                        ->maxPrice('60')])
                        ->priceCurrency('SEK'),
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
                    ->name('Sittplats')
                    ->priceSpecification([Schema::priceSpecification()
                        ->name('Sittplats')
                        ->minPrice('20')
                        ->maxPrice('40')])
                        ->priceCurrency('SEK'),
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
                    ->name('Pensionär')
                    ->priceSpecification([
                        Schema::priceSpecification()
                            ->name('Pensionär')
                            ->price('200')
                            ->minPrice('200')
                            ->maxPrice('200')
                            ->priceCurrency('SEK')
                    ]),
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

    #[TestDox('event::offers has one offer if only booking link is present')]
    public function testMakesOfferIfBookingLinkIsPresent()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{"booking_link": "https://booking-link.org"}',
            Schema::event()->offers([
                Schema::offer()
                    ->url('https://booking-link.org')
            ])
        );
    }
}
