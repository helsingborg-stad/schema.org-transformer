<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapPotentialAction::class)]
final class MapPotentialActionTest extends TestCase
{
    #[TestDox('event::potentialAction is constructed from acf.onlineAttendenceUrl')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "onlineAttendenceUrl": "https://example.com/online-event"
                }
            }',
            Schema::event()->potentialAction([
                Schema::action()
                    ->type('JoinAction')
                    ->description('Delta i onlineevent')
                    ->url('https://example.com/online-event')])
        );
    }

    #[TestDox('event::potentialAction(null) when acf.onlineAttendenceUrl is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPotentialAction(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->potentialAction([])
        );
    }
}
