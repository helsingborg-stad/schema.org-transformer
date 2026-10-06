<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

class MapCategories extends AbstractSponsorMapper
{
    public function map(array $data): array
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            array_map(fn (array $row) => $row['name'], $acf),
            'resources'
        );
    }
}
