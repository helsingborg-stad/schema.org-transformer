<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;

#[CoversClass(EventsSearchClientOnTypesense::class)]
final class EventsSearchClientOnTypesenseTest extends TestCase
{
    #[TestDox('it should escape and quote queries')]
    #[TestWith(['', '""'])]
    #[TestWith(['school name', '"school name"'])]
    #[TestWith(['school"name', '"school\\"name"'])]
    #[TestWith(['"school" name', '"\\"school\\" name"'])]
    public function testEscapeAndQuoteQuery(string $input, string $expected): void
    {
        $this->assertEquals($expected, (new EventsSearchClientOnTypesense($this->createMock(\Typesense\Client::class)))->escapeAndQuoteQuery($input));
    }
}
