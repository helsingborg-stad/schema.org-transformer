<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapPhysicalAccessibilityFeatures extends AbstractWPHeadlessEventMapper
{
    private array $featureMap = [
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
            array_values(
                array_filter(
                    array_map(
                        fn ($term) =>
                            ($term['taxonomy'] ?? null) === 'accessibility'
                            ? $term['name']
                            : null,
                        ($data['_embedded']['acf:term'] ?? []),
                    )
                )
            )
        );
    }
}
