<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapEvent;

#[CoversClass(MapEvent::class)]
final class MapEventTest extends TestCase
{
    #[TestDox('preschool::event is taken from event')]
    public function testItWorks()
    {
        $mockEventSearchClient = new class implements \SchemaTransformer\Transforms\School\Events\EventsSearchClient {
            public function searchEventsBySchoolName(string $schoolName): array
            {
                return [
                [
                    '@context' => [
                        'schema'    => 'https://schema.org',
                        'municipio' => 'https://schema.municipio.tech/schema.jsonld'
                    ],
                    '@type'    => 'Event',
                    'name'     => 'Skolfest',
                    ],
                    [
                    '@context'  => [
                        'schema'    => 'https://schema.org',
                        'municipio' => 'https://schema.municipio.tech/schema.jsonld'
                    ],
                        '@type' => 'Event',
                        'name'  => 'Idrottsdag',
                    ],
                ];
            }
        };

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEvent($mockEventSearchClient),
            '
            {
                "acf": {}
            }
            ',
            Schema::preschool()->event([
            Schema::event()->name('Skolfest')->toArray(),
            Schema::event()->name('Idrottsdag')->toArray(),
            ])
        );
    }
}
