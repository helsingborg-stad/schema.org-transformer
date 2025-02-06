<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyStartDate implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $occasion = $data['acf']['occasions'][0] ?? null;

        if (empty($occasion) || empty($occasion['date']) || empty($occasion['startTime'])) {
            return null;
        }

        $timeString = $occasion['date'] . 'T' . $occasion['startTime'];
        $timestamp  = strtotime($timeString);

        return $event->setProperty('startDate', $this->formatDate($timestamp));
    }

    private function formatDate(int $timestamp): string
    {
        return date('c', $timestamp);
    }
}
