<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\ImageObjectContract;
use Spatie\SchemaOrg\Contracts\PlaceContract;
use Spatie\SchemaOrg\Schema;

class WPReleaseEventTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $events = array_map(fn($row) => $this->getEventFromRow($row), $data);
        $events = array_filter($events); // Remove null values

        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): ?BaseType
    {
        if (!$this->rowIsValid($row)) {
            return null;
        }

        return $this->populateEventWithRowData($this->createEventTypeFromRow($row), $row);
    }

    private function populateEventWithRowData(BaseType $event, array $row): BaseType
    {
        $event->identifier($this->formatId($row['id']));
        $event->name($row['title']['rendered']);
        $event->description($this->getDescriptionFromRow($row));
        $event->image($this->getImageFromRow($row));
        $event->typicalAgeRange($this->getTypicalAgeRange($row));
        $event->location($this->getLocationFromRow($row));
        $event->offers($this->getOffersFromRow($row));
        $event->isAccessibleForFree($this->getIsAccessibleForFreeFromRow($row));

        return $event;
    }

    private function createEventTypeFromRow(array $row): BaseType
    {
        $eventTypeMap = [
            'BusinessEvent'    => Schema::businessEvent(),
            'ChildrensEvent'   => Schema::childrensEvent(),
            'ComedyEvent'      => Schema::comedyEvent(),
            'DanceEvent'       => Schema::danceEvent(),
            'DeliveryEvent'    => Schema::deliveryEvent(),
            'EducationEvent'   => Schema::educationEvent(),
            'EventSeries'      => Schema::eventSeries(),
            'ExhibitionEvent'  => Schema::exhibitionEvent(),
            'Festival'         => Schema::festival(),
            'FoodEvent'        => Schema::foodEvent(),
            'Hackathon'        => Schema::hackathon(),
            'LiteraryEvent'    => Schema::literaryEvent(),
            'MusicEvent'       => Schema::musicEvent(),
            'PublicationEvent' => Schema::publicationEvent(),
            'SaleEvent'        => Schema::saleEvent(),
            'ScreeningEvent'   => Schema::screeningEvent(),
            'SocialEvent'      => Schema::socialEvent(),
            'SportsEvent'      => Schema::sportsEvent(),
            'TheaterEvent'     => Schema::theaterEvent(),
            'VisualArtsEvent'  => Schema::visualArtsEvent(),
        ];

        return $eventTypeMap[$row['acf']['type'] ?? ''] ?? Schema::event();
    }

    /**
     * Check if the row is valid and can be transformed
     *
     * @param array $row
     */
    private function rowIsValid(array $row): bool
    {
        if (empty($row['id'])) {
            return false;
        }

        if (empty($row['title']['rendered'])) {
            return false;
        }

        return true;
    }

    private function getDescriptionFromRow(array $row): ?string
    {
        return $row['acf']['description'] ?? null;
    }

    private function getImageFromRow(array $row): ?ImageObjectContract
    {
        if (empty($row['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            return null;
        }

        $image = Schema::imageObject();
        $image->url($row['_embedded']['wp:featuredmedia'][0]['source_url']);
        $image->description($row['_embedded']['wp:featuredmedia'][0]['alt_text']);

        return $image;
    }

    private function getTypicalAgeRange(array $row): ?string
    {
        if ($this->hasAgeRestriction($row)) {
            return $row['acf']['age_restriction_info'];
        }

        return null;
    }

    private function hasAgeRestriction(array $row): bool
    {
        return !empty($row['acf']['age_restriction']) && $row['acf']['age_restriction'] !== false && !empty($row['acf']['age_restriction_info']);
    }

    private function getLocationFromRow(array $row): ?PlaceContract
    {
        if (!$this->isPhysicalLocation($row)) {
            return null;
        }

        return $this->createPlaceFromRow($row);
    }

    private function isPhysicalLocation(array $row): bool
    {
        return !empty($row['acf']['location']) &&
               !empty($row['acf']['physical_virtual']) &&
               $row['acf']['physical_virtual'] === 'physical';
    }

    private function createPlaceFromRow(array $row): PlaceContract
    {
        $place = Schema::place();
        $place->name($row['acf']['location_name'] ?? null);
        $place->address($row['acf']['location']['address'] ?? null);
        $place->latitude($row['acf']['location']['lat'] ?? null);
        $place->longitude($row['acf']['location']['lng'] ?? null);

        return $place;
    }

    private function getOffersFromRow(array $row): array
    {
        if ($this->isFreeEvent($row) || !$this->hasPricesList($row)) {
            return [];
        }

        return array_map([$this, 'createOfferFromPriceRow'], $row['acf']['pricesList']);
    }

    private function isFreeEvent(array $row): bool
    {
        return empty($row['acf']['pricing']) || $row['acf']['pricing'] !== 'expense';
    }

    private function hasPricesList(array $row): bool
    {
        return !empty($row['acf']['pricesList']);
    }

    private function createOfferFromPriceRow(array $priceRow): BaseType
    {
        return Schema::offer()
            ->price($priceRow['price'] ?? null)
            ->name($priceRow['priceLabel'] ?? null)
            ->priceCurrency('SEK');
    }

    private function getIsAccessibleForFreeFromRow(array $row): bool
    {
        return !empty($row['acf']['pricing']) && $row['acf']['pricing'] === 'free';
    }
}
