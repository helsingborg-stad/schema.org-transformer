<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;

class WPReleaseEventTransform extends TransformBase implements AbstractDataTransform
{
    /**
     * WPReleaseEventTransform constructor.
     *
     * @param string $idprefix
     * @param AbstractDataTransform $splitRowsByOccasion
     * @param SchemaDecorator[] $eventDecorators
     */
    public function __construct(
        string $idprefix,
        private AbstractDataTransform $splitRowsByOccasion,
        private array $eventDecorators = []
    ) {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $rows   = $this->splitRowsByOccasion->transform($data);
        $events = array_map(fn($row) => $this->getEventFromRow($row), $rows);
        $events = array_filter($events); // Remove null values

        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): ?BaseType
    {
        $event = $this->populateEventWithRowData($this->createEventTypeFromRow($row), $row);

        return $this->eventMeetsRequirements($event) ? $event : null;
    }

    private function populateEventWithRowData(BaseType $event, array $row): BaseType
    {
        $event->identifier(!empty($row['id']) ? $this->formatId($row['id']) : null);

        foreach ($this->eventDecorators as $decorator) {
            $event = $decorator->apply($event, $row);
        }

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
    private function eventMeetsRequirements(BaseType $event): bool
    {
        if (is_null($event->getProperty('identifier'))) {
            return false;
        }

        if (is_null($event->getProperty('name'))) {
            return false;
        }

        return true;
    }
}
