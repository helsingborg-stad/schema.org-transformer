<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;
use Municipio\Schema\SponsorOffer;

class MapOffer extends AbstractMapper
{
    private const RESOURCE_KEY = 'resources';

    protected function mapCategories(array $data): array
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            array_map(fn (array $row) => $row['name'] ?? null, $acf),
            self::RESOURCE_KEY
        ) ?? [];
    }

    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        $acf  = is_array($data['acf'] ?? null) ? $data['acf'] : [];
        $date = $this->mapDateTime($acf['due_date'] ?? null, $acf['due_time'] ?? null);

        return $offer
            ->identifier($data['id'] ?? '')
            ->name($data['title']['rendered'] ?? '')
            ->availabilityEnds($date?->format('Y-m-d\TH:i:s') ?? '')
            ->category($this->mapCategories($data));
    }
}
