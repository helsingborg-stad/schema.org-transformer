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
    public function testMappedFromDates()
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
                                ->price([195]),
                            Schema::priceSpecification()
                                ->name('Sofierokortet 2025')
                                ->description('Sofierokortet 2025')
                                ->price([145])
                        ])
                ])
        );
    }
/*
    public function testMappedFromEventGroup()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOffers(),
            '{
                "EventId": 456,
                "DefaultEventGroupId": 123,
                "OnlineSaleStart": "2025-09-03T12:00:00+02:00",
                "OnlineSaleEnd": "2025-09-27T23:00:00+02:00",
                "ProductPurchaseUrls": [
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
                        ->url('https://example.com/se/tickets/1234')
                        ->mainEntityOfPage('https://example.com/se/tickets/12345')
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->priceSpecification([
                            Schema::priceSpecification()
                                ->name('Ordinarie')
                                ->description('Ordinarie')
                                ->price([195]),
                            Schema::priceSpecification()
                                ->name('Kulturort')
                                ->description('Kulturort')
                                ->price([145])
                        ])
                ])
        );
    }
*/
}
