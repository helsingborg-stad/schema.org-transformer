<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyEndDate implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $occasion = $data['occasions'][0] ?? null;

        if (empty($occasion) || empty($occasion['end_date'])) {
            return $event;
        }

        $timestamp = strtotime($occasion['end_date']);

        return $event->setProperty('endDate', $this->formatDate($timestamp));
    }

    private function formatDate(int $timestamp): string
    {
        return date('c', $timestamp);
    }
}
