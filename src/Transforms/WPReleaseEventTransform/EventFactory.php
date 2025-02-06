<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform;

use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class EventFactory implements SchemaFactory
{
    public function createSchema(array $data): BaseType
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

        return $eventTypeMap[$data['acf']['type'] ?? ''] ?? Schema::event();
    }
}
