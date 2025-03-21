<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractIdFormatter;
use SchemaTransformer\Interfaces\SchemaFactory;
use SchemaTransformer\Interfaces\SchemaValidator;
use Spatie\SchemaOrg\BaseType;

class WPLegacyEventTransform implements AbstractDataTransform
{
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
        return array_map(fn($event) => $event->toArray(), array_filter(
            array_map(
                fn($dataRow) => $this->getEventFromRow($dataRow),
                $this->splitRowsByOccasion->transform($data)
            ),
            [$this->schemaValidator, 'isValid']
        ));
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
