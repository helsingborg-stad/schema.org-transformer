<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapImage extends AbstractMapper
{
    private const IMAGE_KEY = 'image';

    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        return $event->image(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                Schema::imageObject()
                    ->url($acf['url'] ?? null)
                    ->name($acf['title'] ?? null)
                    ->description($acf['alt'] ?? null),
                self::IMAGE_KEY
            )
        ) ?? $event;
    }
}
