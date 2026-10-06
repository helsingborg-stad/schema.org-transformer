<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapContactPoint;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapDemand;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapImage;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapLocation;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOffer;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOrganization;

class SponsorDemandTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapDemandEvent(),
            new MapImage(),
            new MapContactPoint(),
            new MapDemand(),
            new MapLocation(),
            new MapOffer(),
            new MapOrganization(),
            new MapKeywords(),
        ];
        return array_map(function ($row) use ($mappers) {
            [$event, $image, $contactPoint, $demand, $location, $offer, $organization, $keywords] = $mappers;

            return $event->map($row)
                ->image($image->map($row))
                ->location($location->map($row))
                ->hasSponsorshipOffer($offer->map($row)->demand($demand->map($row)))
                ->organisation($organization->map($row)->contactPoint($contactPoint->map($row)))
                ->keywords([$keywords->createKeyword($row, 'organization_eligible_for_grants')]);
        }, $data);
    }
}
