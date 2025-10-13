<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapOffers;

#[CoversClass(MapOffers::class)]
final class MapOffersTest extends TestCase
{
    #[TestDox('event::offers is set from source->Dates->PurchaseUrls and Prices')]
    public function testMappedPurchasesFromDates()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "EventGroupId": 26538,
                "Dates": [
                    {
                        "EventId": 110395,
                        "DefaultEventGroupId": 26538,
                        "OnlineSaleStart": "2025-09-03T12:00:00+02:00",
                        "OnlineSaleEnd": "2025-09-27T22:00:00+02:00",
                        "PurchaseUrls": [
                            {
                                "LanguageName": "Svensk",
                                "Culture": "sv-SE",
                                "TwoLetterCulture": "sv",
                                "Link": "https://example.com/se/tickets/1234",
                                "QueueLink": ""
                            },
                            {
                                "LanguageName": "English",
                                "Culture": "en-GB",
                                "TwoLetterCulture": "en",
                                "Link": "https://example.com/en/tickets/1234",
                                "QueueLink": ""
                            }
                        ],
                        "Prices": [
                            {
                                "TicketType": "Ordinarie",
                                "Prices": [
                                    {
                                        "Price": 195
                                    }
                                ]
                            },
                            {
                                "TicketType": "Sofierokortet 2025",
                                "Prices": [
                                    {
                                        "Price": 145
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }',
            Schema::event()
                ->offers([
                    Schema::offer()
                        ->url('https://example.com/se/tickets/1234')
                        ->mainEntityOfPage('https://example.com/se/tickets/1234')
                        ->availabilityStarts('2025-09-03T12:00:00+02:00')
                        ->availabilityEnds('2025-09-27T22:00:00+02:00')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->priceSpecification([
                            Schema::priceSpecification()
                                ->name('Ordinarie')
                                ->description('Ordinarie')
                                ->priceCurrency('SEK')
                                ->price(195),
                            Schema::priceSpecification()
                                ->name('Sofierokortet 2025')
                                ->description('Sofierokortet 2025')
                                ->priceCurrency('SEK')
                                ->price(145)
                        ])
                ])
        );
    }

    #[TestDox('event::offers is set from source when source is Date like object')]
    public function testMappedPurchasesFromEventGroup()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "EventId": 456,
                "DefaultEventGroupId": 123,
                "OnlineSaleStart": "2025-09-03T12:00:00+02:00",
                "OnlineSaleEnd": "2025-09-27T23:00:00+02:00",
                "PurchaseUrls": [
                    {
                        "LanguageName": "Svensk",
                        "Culture": "sv-SE",
                        "TwoLetterCulture": "sv",
                        "Link": "https://example.com/se/tickets/12345",
                        "QueueLink": ""
                    },
                    {
                        "LanguageName": "English",
                        "Culture": "en-GB",
                        "TwoLetterCulture": "en",
                        "Link": "https://example.com/en/tickets/12345",
                        "QueueLink": ""
                    }
                ],
                "Prices": [
                    {
                        "TicketType": "Ordinarie",
                        "Prices": [
                            {
                                "Price": 195
                            }
                        ]
                    },
                    {
                        "TicketType": "Kulturkort",
                        "Prices": [
                            {
                                "Price": 145
                            }
                        ]
                    }
                ]
            }',
            Schema::event()
                ->offers([
                    Schema::offer()
                        ->url('https://example.com/se/tickets/12345')
                        ->mainEntityOfPage('https://example.com/se/tickets/12345')
                        ->availabilityStarts('2025-09-03T12:00:00+02:00')
                        ->availabilityEnds('2025-09-27T23:00:00+02:00')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->priceSpecification([
                            Schema::priceSpecification()
                                ->name('Ordinarie')
                                ->description('Ordinarie')
                                ->price(195)
                                ->priceCurrency('SEK'),
                            Schema::priceSpecification()
                                ->name('Kulturkort')
                                ->description('Kulturkort')
                                ->price(145)
                                ->priceCurrency('SEK')
                        ])
                ])
        );
    }

    #[TestDox('event::offers is set to products from source when source has product and product urls when includeProducts=true')]
    public function testMappedProductsFromDates()
    {
        $includeProducts = true;

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers($includeProducts),
            '{
                "EventGroupId": 26538,
                "Dates": [
                    {
                        "EventId": 110395,
                        "DefaultEventGroupId": 26538,
                        "ProductPurchaseUrls": [
                            {
                                "LanguageName": "Svensk",
                                "Culture": "sv-SE",
                                "TwoLetterCulture": "sv",
                                "Link": "https://example.com/se/products/1234",
                                "QueueLink": ""
                            },
                            {
                                "LanguageName": "English",
                                "Culture": "en-GB",
                                "TwoLetterCulture": "en",
                                "Link": "https://example.com/en/products/1234",
                                "QueueLink": ""
                            }
                        ],
                        "Products": [
                            {
                                "ProductId": 3841,
                                "Name": "Stora fina boken",
                                "Description": "<p>Med inspiration från galaxen</p>",
                                "Price": 265,
                                "ProductImagePath": "https://example.org/book.jpg"
                            }                        
                        ]
                    }
                ]
            }',
            Schema::event()
                ->offers([
                    Schema::offer()
                        ->url('https://example.com/se/products/1234')
                        ->name('Stora fina boken')
                        ->description('<p>Med inspiration från galaxen</p>')
                        ->mainEntityOfPage('https://example.com/se/products/1234')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->price(265)
                        ->priceCurrency('SEK')
                        ->image(
                            Schema::imageObject()
                                ->name('Stora fina boken')
                                ->description('Stora fina boken')
                                ->caption('Stora fina boken')
                                ->url('https://example.org/book.jpg')
                        )
                ])
        );
    }

    #[TestDox('event::offers does not contain products from source when source has product and product urls when includeProducts=false')]
    public function testNotMappedProductsFromDates()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(/* $includeProducts = false */),
            '{
                "EventGroupId": 26538,
                "Dates": [
                    {
                        "EventId": 110395,
                        "DefaultEventGroupId": 26538,
                        "ProductPurchaseUrls": [
                            {
                                "LanguageName": "Svensk",
                                "Culture": "sv-SE",
                                "TwoLetterCulture": "sv",
                                "Link": "https://example.com/se/products/1234",
                                "QueueLink": ""
                            },
                            {
                                "LanguageName": "English",
                                "Culture": "en-GB",
                                "TwoLetterCulture": "en",
                                "Link": "https://example.com/en/products/1234",
                                "QueueLink": ""
                            }
                        ],
                        "Products": [
                            {
                                "ProductId": 3841,
                                "Name": "Stora fina boken",
                                "Description": "<p>Med inspiration från galaxen</p>",
                                "Price": 265,
                                "ProductImagePath": "https://example.org/book.jpg"
                            }                        
                        ]
                    }
                ]
            }',
            Schema::event()
                ->offers([])
        );
    }

    #[TestDox('a realistic example with a mix of purchases and products taken from souce->Dates when includeProducts=true')]
    public function testBigOneWithPurchaseAndProductInDates()
    {
        $includeProducts = true;

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers($includeProducts),
            '{
                "EventGroupId": 26538,
                "Dates": [
                    {
                        "EventId": 110395,
                        "DefaultEventGroupId": 26538,
                        "OnlineSaleStart": "2025-09-03T12:00:00+02:00",
                        "OnlineSaleEnd": "2025-09-27T22:00:00+02:00",
                        "PurchaseUrls": [
                            {
                                "LanguageName": "Svensk",
                                "Culture": "sv-SE",
                                "TwoLetterCulture": "sv",
                                "Link": "https://example.com/se/tickets/1234",
                                "QueueLink": ""
                            },
                            {
                                "LanguageName": "English",
                                "Culture": "en-GB",
                                "TwoLetterCulture": "en",
                                "Link": "https://example.com/en/tickets/1234",
                                "QueueLink": ""
                            }
                        ],
                        "Prices": [
                            {
                                "TicketType": "Ordinarie",
                                "Prices": [
                                    {
                                        "Price": 195
                                    }
                                ]
                            },
                            {
                                "TicketType": "Sofierokortet 2025",
                                "Prices": [
                                    {
                                        "Price": 0
                                    }
                                ]
                            }
                        ],
                        "ProductPurchaseUrls": [
                            {
                                "LanguageName": "Svensk",
                                "Culture": "sv-SE",
                                "TwoLetterCulture": "sv",
                                "Link": "https://example.com/se/products/1234",
                                "QueueLink": ""
                            },
                            {
                                "LanguageName": "English",
                                "Culture": "en-GB",
                                "TwoLetterCulture": "en",
                                "Link": "https://example.com/en/products/1234",
                                "QueueLink": ""
                            }
                        ],
                        "Products": [
                            {
                                "ProductId": 3841,
                                "Name": "Stora fina boken",
                                "Description": "<p>Med inspiration från galaxen</p>",
                                "Price": 265,
                                "ProductImagePath": "https://example.org/book.jpg"
                            }                        
                        ]
                    }
                ]
            }',
            Schema::event()
                ->offers([
                    Schema::offer()
                        ->url('https://example.com/se/tickets/1234')
                        ->mainEntityOfPage('https://example.com/se/tickets/1234')
                        ->availabilityStarts('2025-09-03T12:00:00+02:00')
                        ->availabilityEnds('2025-09-27T22:00:00+02:00')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->priceSpecification([
                            Schema::priceSpecification()
                                ->name('Ordinarie')
                                ->description('Ordinarie')
                                ->price(195)
                                ->priceCurrency('SEK'),
                            Schema::priceSpecification()
                                ->name('Sofierokortet 2025')
                                ->description('Sofierokortet 2025')
                                ->price(0)
                                ->priceCurrency('SEK')
                        ]),
                    Schema::offer()
                        ->url('https://example.com/se/products/1234')
                        ->name('Stora fina boken')
                        ->description('<p>Med inspiration från galaxen</p>')
                        ->mainEntityOfPage('https://example.com/se/products/1234')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->price(265)
                        ->priceCurrency('SEK')
                        ->image(
                            Schema::imageObject()
                                ->name('Stora fina boken')
                                ->description('Stora fina boken')
                                ->caption('Stora fina boken')
                                ->url('https://example.org/book.jpg')
                        )

                ])
        );
    }
}
