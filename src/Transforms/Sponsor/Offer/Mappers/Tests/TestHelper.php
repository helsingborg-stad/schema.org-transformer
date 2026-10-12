<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

/** Load sponsor API responses used by mapper tests. */
final class TestHelper
{
    /**
     * Load the first entry in a sponsor response fixture.
     *
     * @return array<string, mixed>
     */
    public static function fixture(string $filename): array
    {
        return json_decode(file_get_contents(__DIR__ . '/' . $filename), true, 512, JSON_THROW_ON_ERROR)[0];
    }
}
