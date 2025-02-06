<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\BaseType;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaFactory;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaValidator;

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
        private SchemaFactory $schemaFactory,
        private array $eventDecorators,
        private SchemaValidator $schemaValidator
    ) {
        parent::__construct($idprefix);
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
        $event->identifier(!empty($row['id']) ? $this->formatId($row['id']) : null);

        foreach ($this->eventDecorators as $decorator) {
            $event = $decorator->apply($event, $row);
        }

        return $event;
    }
}
