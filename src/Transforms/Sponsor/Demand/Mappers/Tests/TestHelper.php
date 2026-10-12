<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

final class TestHelper
{
    /**
     * Load the first entry in a sponsor demand response fixture.
     *
     * @return array<string, mixed>
     */
    public static function fixture(string $filename = 'demand.data.json'): array
    {
        return json_decode(file_get_contents(__DIR__ . '/' . $filename), true, 512, JSON_THROW_ON_ERROR)[0];
    }
}
