<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyPhysicalAccessibilityFeatures implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($data['accessibility'])) {
            return $event;
        }


        $event->setProperty('municipio:physicalAccessibilityFeatures', $this->prepareNames($data['accessibility']));

        return $event;
    }

    private function prepareNames(array $accessibility): array
    {
        $map = ['Accessible toilet' => 'Handikapptoalett', 'Elevator/ramp' => 'Hiss/ramp'];

        return array_map(function ($term) use ($map) {
            return $map[$term] ?? $term;
        }, $accessibility);
    }
}
