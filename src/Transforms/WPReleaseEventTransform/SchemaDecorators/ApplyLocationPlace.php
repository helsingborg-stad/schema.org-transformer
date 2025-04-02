<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Interfaces\PathValueAccessor;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyLocationPlace implements SchemaDecorator
{
    public function __construct(
        private string $namePath,
        private string $addressPath,
        private string $latPath,
        private string $lngPath,
        private PathValueAccessor $pathValueAccessor
    ) {
    }

    public function apply(BaseType $event, array $data): BaseType
    {
        $address = $this->pathValueAccessor->getValue($data, $this->addressPath);
        $lat     = $this->pathValueAccessor->getValue($data, $this->latPath);
        $lng     = $this->pathValueAccessor->getValue($data, $this->lngPath);

        // If lat and lng does not exist. Check if address exists, and if not, return the event as is.
        if (empty($lat) || empty($lng)) {
            if (empty($address)) {
                return $event;
            }
        }

        return $event->setProperty('location', Schema::place()
            ->name($this->pathValueAccessor->getValue($data, $this->namePath))
            ->address($address)
            ->latitude($lat)
            ->longitude($lng));
    }
}
