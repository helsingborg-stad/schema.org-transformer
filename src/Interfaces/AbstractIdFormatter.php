<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractIdFormatter
{
    /**
     * Format the ID to be used in the schema.
     *
     * @param string $id The ID to format
     * @return string The formatted ID
     */
    public function formatId(string $id): string;
}
