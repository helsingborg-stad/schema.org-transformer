<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyEndDate implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $occasion = $data['acf']['occasions'][0] ?? null;

        if (empty($occasion) || empty($occasion['date']) || empty($occasion['endTime'])) {
            return $event;
        }

        $timeString = $occasion['date'] . 'T' . $occasion['endTime'];
        $timestamp  = strtotime($timeString);

        return $event->setProperty('endDate', $this->formatDate($timestamp));
    }

    private function formatDate(int $timestamp): string
    {
        return date('c', $timestamp);
    }
}
