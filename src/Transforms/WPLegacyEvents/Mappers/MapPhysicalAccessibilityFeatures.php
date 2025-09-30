<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapPhysicalAccessibilityFeatures extends AbstractWPLegacyEventMapper
{
    private array $termMap = [
        'Accessible toilet' => 'Handikapptoalett',
        'Elevator/ramp'     => 'Hiss/ramp',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->physicalAccessibilityFeatures(
            array_map(
                fn($term) => $this->termMap[$term] ?? $term,
                $data['accessibility'] ?? []
            )
        );
    }
}
