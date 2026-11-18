<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor;

use DateTimeImmutable;
use DateTimeZone;
use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemand;
use Municipio\Schema\SponsorOffer;

class MapWebPage
{
    public static function listingId(array $data): ?string
    {
        // WordPress GUIDs are stable across permalink changes.
        $guid = html_entity_decode($data['guid']['rendered'] ?? '', ENT_QUOTES | ENT_HTML5);
        return filter_var($guid, FILTER_VALIDATE_URL) ? $guid : null;
    }

    public function map(SponsorDemand|SponsorOffer $listing, array $data): SponsorDemand|SponsorOffer
    {
        $id = $listing->getProperty('@id');
        $published = $data['date_gmt'] ?? null;
        $dueDate = $data['acf']['due_date'] ?? null;
        if (!$id || !$published || !$dueDate) {
            return $listing;
        }

        $expires = DateTimeImmutable::createFromFormat('!Y-m-d', $dueDate, new DateTimeZone('Europe/Stockholm'));
        if (!$expires || $expires->format('Y-m-d') !== $dueDate) {
            return $listing;
        }

        return $listing->mainEntityOfPage(
            Schema::webPage()
                ->setProperty('@id', $id . '#page')
                ->mainEntity(['@id' => $id])
                ->datePublished($published . 'Z')
                ->expires($expires->modify('+1 day')->format(DATE_ATOM))
        );
    }
}
