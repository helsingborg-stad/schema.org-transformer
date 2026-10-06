<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapImage extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\ImageObject
    {
        return $this->withAcfFields(
            $data,
            fn(array $acf) =>
            Schema::imageObject()
                ->url($acf['url'] ?? null)
                ->name($acf['title'] ?? null)
                ->description($acf['alt'] ?? null),
            'image'
        );
    }
}
