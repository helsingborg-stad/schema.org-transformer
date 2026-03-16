<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapOffers::class)]
final class MapOffersTest extends TestCase
{
    #[TestDox('event::offers is constructed from acf.pricesList')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "pricesList": [
                        {
                            "priceLabel": "Standard",
                            "price": "100"
                        },
                        {
                            "priceLabel": "Ungdom",
                            "price": "50"
                        }
                    ]
                }
            }',
            Schema::event()->offers([
                Schema::offer()
                    ->name('Standard')
                    ->price(100)
                    ->priceCurrency('SEK')
                    ->url(null)
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell'),
                Schema::offer()
                    ->name('Ungdom')
                    ->price(50)
                    ->priceCurrency('SEK')
                    ->url(null)
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell')
            ])
        );
    }

    #[TestDox('event::offers is constructed from acf.pricesList and booking link is taken from occasions')]
    public function testItWorksWithBookingLink()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "url": "https://example.com/tickets"
                        }
                    ],
                    "pricesList": [
                        {
                            "priceLabel": "Standard",
                            "price": "100"
                        },
                        {
                            "priceLabel": "Ungdom",
                            "price": "50"
                        }
                    ]
                }
            }',
            Schema::event()->offers([
                Schema::offer()
                    ->name('Standard')
                    ->price(100)
                    ->priceCurrency('SEK')
                    ->url('https://example.com/tickets')
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell'),
                Schema::offer()
                    ->name('Ungdom')
                    ->price(50)
                    ->priceCurrency('SEK')
                    ->url('https://example.com/tickets')
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell')
            ])
        );
    }

    #[TestDox('event::offers([]) when acf.pricesList is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->offers([])
        );
    }

    #[TestDox('event::offers with url from occasions but no prices')]
    public function testOffersWithUrlFromOccasions()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "url": ""
                        },
                        {
                             "no_url": "...so this should be ignored"
                        },
                        {
                            "url": "https://example.com/tickets"
                        },
                        {
                            "url": "this one came second and missed its chance"
                        }
                    ]
                }
            }',
            Schema::event()->offers([
                Schema::offer()->url('https://example.com/tickets')
            ])
        );
    }
}
