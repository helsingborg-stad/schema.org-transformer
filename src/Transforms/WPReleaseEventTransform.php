<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
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
        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): Event
    {
        return Schema::event();
    }
}
