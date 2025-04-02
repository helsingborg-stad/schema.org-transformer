<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Contracts\PlaceContract;
use Municipio\Schema\Contracts\VirtualLocationContract;
use Municipio\Schema\Schema;

class ApplyLocation implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if ($this->isPhysicalLocation($data)) {
            return $event->setProperty('location', $this->createPlaceFromRow($data));
        }

        if ($this->isVirtualLocation($data)) {
            return $event->setProperty('location', $this->createVirtualLocationFromRow($data));
        }

        return $event;
    }

    private function isPhysicalLocation(array $data): bool
    {
        return !empty($data['acf']['location']) &&
               !empty($data['acf']['physical_virtual']) &&
               $data['acf']['physical_virtual'] === 'physical';
    }

    private function isVirtualLocation(array $data): bool
    {
        return !empty($data['acf']['location']) &&
               !empty($data['acf']['physical_virtual']) &&
               $data['acf']['physical_virtual'] === 'virtual';
    }

    private function createPlaceFromRow(array $data): PlaceContract
    {
        return Schema::place()
            ->name($data['acf']['location_name'] ?? null)
            ->address($data['acf']['location']['address'] ?? null)
            ->latitude($data['acf']['location']['lat'] ?? null)
            ->longitude($data['acf']['location']['lng'] ?? null);
    }

    private function createVirtualLocationFromRow(array $data): VirtualLocationContract
    {
        return Schema::virtualLocation()
            ->url($data['acf']['meeting_link'] ?? null)
            ->description($data['acf']['connect'] ?? null);
    }
}
