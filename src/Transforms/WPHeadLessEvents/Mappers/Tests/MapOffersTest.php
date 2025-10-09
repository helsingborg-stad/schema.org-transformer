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
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell'),
                Schema::offer()
                    ->name('Ungdom')
                    ->price(50)
                    ->priceCurrency('SEK')
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
}
