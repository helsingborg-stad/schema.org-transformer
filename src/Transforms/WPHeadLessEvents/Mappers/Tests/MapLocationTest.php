<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('event::location is constructed from acf.locationName, acf.locationAddress')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "locationName": "https://helsingborg.se/",
                    "locationAddress": {
                        "address": "Dunkers kulturhus, Kungsgatan, Helsingborg, Sverige",
                        "lat": 56.0478422,
                        "lng": 12.6890694,
                        "zoom": 14,
                        "place_id": "ChIJhX209zMyUkYR23v1_qmcpyc",
                        "name": "Dunkers kulturhus",
                        "street_number": 11,
                        "street_name": "Kungsgatan",
                        "city": "Helsingborg",
                        "state": "Skåne län",
                        "post_code": "252 21",
                        "country": "Sverige",
                        "country_short": "SE"
                    }     
                }
            }',
            Schema::event()->location([Schema::place()
                ->name('Dunkers kulturhus')
                ->address('Dunkers kulturhus, Kungsgatan, Helsingborg, Sverige')
                ->latitude(56.0478422)
                ->longitude(12.6890694)
                ->url('https://helsingborg.se/')
            ])
        );
    }

    #[TestDox('event::location[] when missing acf.locationAddress.name')]
    public function testMissingName()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "locationName": null,
                    "locationAddress": {
                        "address": "Dunkers kulturhus, Kungsgatan, Helsingborg, Sverige",
                        "lat": 56.0478422,
                        "lng": 12.6890694,
                        "zoom": 14,
                        "place_id": "ChIJhX209zMyUkYR23v1_qmcpyc",
                        "name": null,
                        "street_number": 11,
                        "street_name": "Kungsgatan",
                        "city": "Helsingborg",
                        "state": "Skåne län",
                        "post_code": "252 21",
                        "country": "Sverige",
                        "country_short": "SE"
                    }
                }
            }',
            Schema::event()->location([])
        );
    }

    #[TestDox('event::location[] when missing acf.locationAddress')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->location([])
        );
    }
}
