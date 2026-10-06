<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

class MapCategories extends AbstractSponsorMapper
{
    private const RESOURCE_KEY = 'resources';

    public function map(array $data): array
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            array_map(fn (array $row) => $row['name'], $acf),
            self::RESOURCE_KEY
        );
    }
}
