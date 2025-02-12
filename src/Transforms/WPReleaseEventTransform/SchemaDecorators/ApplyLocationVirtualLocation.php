<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Interfaces\PathValueAccessor;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyLocationVirtualLocation implements SchemaDecorator
{
    public function __construct(
        private string $urlPath,
        private string $descriptionPath,
        private PathValueAccessor $pathValueAccessor
    ) {
    }

    public function apply(BaseType $event, array $data): BaseType
    {
        $url = $this->pathValueAccessor->getValue($data, $this->urlPath);

        if ($url) {
            return $event->setProperty('location', Schema::virtualLocation()
                ->url($url)
                ->description($this->pathValueAccessor->getValue($data, $this->descriptionPath)));
        }

        return $event;
    }
}
