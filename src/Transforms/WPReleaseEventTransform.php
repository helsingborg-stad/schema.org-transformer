<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractIdFormatter;
use SchemaTransformer\Interfaces\SchemaFactory;
use SchemaTransformer\Interfaces\SchemaValidator;
use Municipio\Schema\BaseType;

class WPReleaseEventTransform implements AbstractDataTransform
{
    /**
     * WPReleaseEventTransform constructor.
     */
    public function __construct(
        private AbstractIdFormatter $idFormatter,
        private AbstractDataTransform $splitRowsByOccasion,
        private SchemaFactory $schemaFactory,
        private array $eventDecorators,
        private SchemaValidator $schemaValidator
    ) {
    }

    public function transform(array $data): array
    {
        $rows   = $this->splitRowsByOccasion->transform($data);
        $events = array_map(fn($dataRow) => $this->getEventFromRow($dataRow), $rows);
        $events = array_filter($events, [$this->schemaValidator, 'isValid']);

        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): ?BaseType
    {
        $event = $this->schemaFactory->createSchema($row);
        $event->identifier($row['id']);

        foreach ($this->eventDecorators as $decorator) {
            $event = $decorator->apply($event, $row);
        }

        return $event;
    }
}
