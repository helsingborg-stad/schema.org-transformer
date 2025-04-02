<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyTypicalAgeRange implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (!$this->hasAgeRestriction($data)) {
            return $event;
        }

        return $event->setProperty('typicalAgeRange', $this->getAgeRange($data));
    }

    private function hasAgeRestriction(array $row): bool
    {
        return !empty($row['age_group_from']) || !empty($row['age_group_to']);
    }

    private function getAgeRange(array $row): string
    {
        $from = $row['age_group_from'] ?? '';
        $to   = $row['age_group_to'] ?? '';

        if ($from && $to) {
            return $from . '-' . $to;
        }

        if ($from) {
            return $from . '+';
        }

        if ($to) {
            return '0-' . $to;
        }

        return '';
    }
}
