<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    /** Verify that all offering activities become defined terms in source order. */
    public function testMapsOfferingActivities(): void
    {
        $data = TestHelper::fixture('offering.data.json');

        $keywords = (new MapKeywords())->map($data);

        self::assertEquals([
            Schema::definedTerm()->name('cykling')->inDefinedTermSet(Schema::definedTermSet()->name('activity')),
            Schema::definedTerm()->name('e-sport')->inDefinedTermSet(Schema::definedTermSet()->name('activity')),
        ], $keywords);
    }

    /** Verify that empty activities produce no keywords. */
    public function testMapsEmptyActivities(): void
    {
        $data                      = TestHelper::fixture('offering.data.json');
        $data['acf']['activities'] = [];

        self::assertSame([], (new MapKeywords())->map($data));
    }

    /** Verify that an individual ACF field can become a defined term. */
    public function testCreatesKeywordFromAcfField(): void
    {
        $data = TestHelper::fixture('demand.data.json');

        self::assertEquals(
            Schema::definedTerm()->name('Test krav')->inDefinedTermSet(Schema::definedTermSet()->name('requirements')),
            (new MapKeywords())->createKeyword($data, 'requirements')
        );
    }
}
