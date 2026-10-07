<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;

class MapKeywords extends AbstractMapper
{
    private const ACTIVITIES_KEY = 'activities';

    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        return $offer->keywords(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                    array_map(
                        fn($row) => Schema::definedTerm()
                            ->name($row['name'] ?? null)
                            ->inDefinedTermSet(Schema::definedTermSet()->name($row['taxonomy'] ?? null)),
                        $acf
                    ),
                self::ACTIVITIES_KEY
            )
        );
    }
}
