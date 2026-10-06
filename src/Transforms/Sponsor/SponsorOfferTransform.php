<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapContactPoint;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapDemand;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapImage;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapLocation;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOffer;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOrganization;

class SponsorOfferTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }
    public function transform(array $data): array
    {
        $mappers = [
        new MapOffer(),
        new MapImage(),
        new MapLocation(),
        new MapKeywords(),
        new MapOrganization(),
        new MapContactPoint(),
        new MapDemand(),
        ];
        return array_map(function ($row) use ($mappers) {
            [$offer, $image, $location, $keywords, $organization, $contactPoint, $demand] = $mappers;

            return $offer->map($row)
            ->identifier($row['id'] ?? '')
            ->name($row['title']['rendered'] ?? '')
            ->image($image->map($row))
            ->location($location->map($row))
            ->keywords($keywords->map($row))
            ->offeredBy(
                $organization->map($row)
                    ->contactPoint($contactPoint->map($row))
                    ->demand($demand->map($row))
                    ->keywords([
                $keywords->createKeyword($row, 'proposal_for_counter_performance')
                ])
            );
        }, $data);
    }
}
