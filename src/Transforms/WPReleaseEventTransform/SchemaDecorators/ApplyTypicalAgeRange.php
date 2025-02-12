<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyTypicalAgeRange implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (!$this->hasAgeRestriction($data)) {
            return $event;
        }

        return $event->setProperty('typicalAgeRange', $data['acf']['age_restriction_info']);
    }

    private function hasAgeRestriction(array $row): bool
    {
        if (empty($row['acf']['age_restriction'])) {
            return false;
        }

        if ($row['acf']['age_restriction'] === false) {
            return false;
        }

        if (empty($row['acf']['age_restriction_info'])) {
            return false;
        }

        return true;
    }
}
