<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor;

final class ListingJsonLd
{
    public static function serialize(array $listing): array
    {
        $listing['@context'] = [
            '@vocab' => 'https://schema.org/',
            'sponsor' => 'https://schema.municipio.tech/schema.jsonld#',
            'SponsorDemand' => 'sponsor:SponsorDemand',
            'SponsorOffer' => 'sponsor:SponsorOffer',
        ];

        foreach ([
            'supportNeededBy', 'supportLocation', 'sponsorPreferences', 'counterPerformance',
            'sponsorshipDate', 'practicalInformation', 'requestedCounterPerformance',
        ] as $property) {
            if (array_key_exists($property, $listing)) {
                $listing['sponsor:' . $property] = $listing[$property];
                unset($listing[$property]);
            }
        }

        return $listing;
    }
}
