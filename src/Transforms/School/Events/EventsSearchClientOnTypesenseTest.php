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
    #[TestWith(['', [], null])]
    #[TestWith(['', [''], null])]
    #[TestWith(['', ['',''], null])]
    #[TestWith(['', ['a','', 'b'], [
        'q'         => '*',
        'query_by'  => 'organizer.name,keywords.name',
        'filter_by' => 'organizer.name:[`a`,`b`]',
    ]])]
    #[TestWith(['', ['Happy School','"illegal quote', 'Happy Children'], [
        'q'         => '*',
        'query_by'  => 'organizer.name,keywords.name',
        'filter_by' => 'organizer.name:[`Happy School`,`Happy Children`]',
    ]])]
    #[TestWith(['Happy School', [], [
        'q'         => '*',
        'query_by'  => 'organizer.name,keywords.name',
        'filter_by' => 'keywords.name:`Happy School`',
    ]])]
    #[TestWith(['n', ['o1','02'], [
        'q'         => '*',
        'query_by'  => 'organizer.name,keywords.name',
        'filter_by' => 'keywords.name:`n` || organizer.name:[`o1`,`02`]',
    ]])]
    public function testCreateTypesenseQuery(string $schoolName, array $eventOrganizations, ?array $expected): void
    {
        $this->assertEquals($expected, (new EventsSearchClientOnTypesense($this->createMock(\Typesense\Client::class)))->createTypesenseQuery($schoolName, $eventOrganizations));
    }

    #[TestWith(['keywords.name', [], null])]
    #[TestWith(['keywords.name', ['a'], 'keywords.name:`a`'])]
    #[TestWith(['keywords.name', ['a','b'], 'keywords.name:[`a`,`b`]'])]
    #[TestWith(['keywords.name', ['é"&\'<>'], null])]
    public function testCreateFilterBy(string $field, array $values, ?string $expected): void
    {
        $this->assertEquals($expected, (new EventsSearchClientOnTypesense($this->createMock(\Typesense\Client::class)))->createFilterBy($field, $values));
    }
}
