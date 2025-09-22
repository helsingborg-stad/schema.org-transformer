<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\TixEvents\TixEventTransform;
use Municipio\Schema\Schema;

#[CoversClass(ElementarySchoolTransform::class)]
final class TixEventTransformTest extends TestCase
{
    private function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    #[TestDox('it doesn\'t break when a lot is missing')]
    public function testAlmostNoSourceData()
    {
        $source        = $this->prepareJsonForTransform('{
            "EventGroupId": 123
            }');
        $expectedEvent = Schema::event()
            ->identifier("tix_123")
            ->image([])
            ->eventSchedule([]);

        $actualEvent = (new TixEventTransform('tix_'))->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent
        );
    }
}
