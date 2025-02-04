<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\EventContract;
use Spatie\SchemaOrg\Contracts\ImageObjectContract;
use Spatie\SchemaOrg\Contracts\PlaceContract;
use Spatie\SchemaOrg\Event;
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
        $events = array_filter($events);

        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): ?BaseType
    {
        if (!$this->rowIsValid($row)) {
            return null;
        }

        $event = $this->createEventTypeFromRow($row);

        $event->identifier($this->formatId($row['id']));
        $event->name($row['title']['rendered']);
        $event->image($this->getImageFromRow($row));
        $event->typicalAgeRange($this->getTypicalAgeRange($row));
        $event->location($this->getLocationFromRow($row));
        $event->offers($this->getOffersFromRow($row));
        $event->isAccessibleForFree(!empty($row['acf']['pricing']) && $row['acf']['pricing'] === 'free' ? true : false);

        return $event;
    }

    private function createEventTypeFromRow(array $row): EventContract
    {
        if (empty($row['acf']['type']) || !is_string($row['acf']['type'])) {
            return Schema::event();
        }

        return match ($row['acf']['type']) {
            'BusinessEvent' => Schema::businessEvent(),
            'ChildrensEvent' => Schema::childrensEvent(),
            'ComedyEvent' => Schema::comedyEvent(),
            'DanceEvent' => Schema::danceEvent(),
            'DeliveryEvent' => Schema::deliveryEvent(),
            'EducationEvent' => Schema::educationEvent(),
            'EventSeries' => Schema::eventSeries(),
            'ExhibitionEvent' => Schema::exhibitionEvent(),
            'Festival' => Schema::festival(),
            'FoodEvent' => Schema::foodEvent(),
            'Hackathon' => Schema::hackathon(),
            'LiteraryEvent' => Schema::literaryEvent(),
            'MusicEvent' => Schema::musicEvent(),
            'PublicationEvent' => Schema::publicationEvent(),
            'SaleEvent' => Schema::saleEvent(),
            'ScreeningEvent' => Schema::screeningEvent(),
            'SocialEvent' => Schema::socialEvent(),
            'SportsEvent' => Schema::sportsEvent(),
            'TheaterEvent' => Schema::theaterEvent(),
            'VisualArtsEvent' => Schema::visualArtsEvent(),
            default => Schema::event(),
        };
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
        if (
            empty($row['acf']['age_restriction']) ||
            $row['acf']['age_restriction'] === false ||
            empty($row['acf']['age_restriction_info'])
        ) {
            return null;
        }

        return $row['acf']['age_restriction_info'];
    }

    private function getLocationFromRow(array $row): ?PlaceContract
    {
        if (
            empty($row['acf']['location']) ||
            empty($row['acf']['physical_virtual']) ||
            $row['acf']['physical_virtual'] !== 'physical'
        ) {
            return null;
        }

        $place = Schema::place();
        $place->name($row['acf']['location_name'] ?? null);
        $place->address($row['acf']['location']['address'] ?? null);
        $place->latitude($row['acf']['location']['lat'] ?? null);
        $place->longitude($row['acf']['location']['lng'] ?? null);

        return $place;
    }

    private function getOffersFromRow(array $row): array
    {
        if (
            empty($row['acf']['pricing']) ||
            $row['acf']['pricing'] !== 'expense' ||
            empty($row['acf']['pricesList'])
        ) {
            return [];
        }

        return array_map(function ($priceRow) {
            return Schema::offer()
                ->price($priceRow['price'] ?? null)
                ->name($priceRow['priceLabel'] ?? null)
                ->priceCurrency('SEK');
        }, $row['acf']['pricesList']);
    }
}
