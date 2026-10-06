<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapCategories;

#[CoversClass(MapCategories::class)]
final class MapCategoriesTest extends TestCase
{
    /**
     * Verify that category names are mapped from each sponsor fixture in source order.
     *
     * @param array<string> $expectedCategories
     */
    #[DataProvider('categoryCases')]
    public function testMapsResourceNamesFromFixture(string $fixture, array $expectedCategories): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/' . $fixture), true, 512, JSON_THROW_ON_ERROR);

        $categories = (new MapCategories())->map($data[0]);

        self::assertSame($expectedCategories, $categories);
    }

    /**
     * Provide the expected category names for demand and offering fixtures.
     *
     * @return array<string, array{string, array<string>}>
     */
    public static function categoryCases(): array
    {
        return [
            'demand'   => ['demand.data.json', ['ekonomi']],
            'offering' => ['offering.data.json', ['stöd och hjälp', 'pengar', 'mat- och dryck', 'logistik', 'övrigt']],
        ];
    }

    /** Verify that an empty ACF resource list does not use the top-level resource IDs. */
    public function testReturnsEmptyArrayWhenAcfResourcesAreEmpty(): void
    {
        $data                        = json_decode(file_get_contents(__DIR__ . '/demand.data.json'), true, 512, JSON_THROW_ON_ERROR);
        $data[0]['acf']['resources'] = [];

        $categories = (new MapCategories())->map($data[0]);

        self::assertSame([], $categories);
    }
}
