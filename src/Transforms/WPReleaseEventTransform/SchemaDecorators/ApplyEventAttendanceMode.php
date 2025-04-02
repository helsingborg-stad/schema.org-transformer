<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyEventAttendanceMode implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (!empty($data['acf']['physical_virtual']) && $data['acf']['physical_virtual'] === 'virtual') {
            return $event->setProperty('eventAttendanceMode', Schema::eventAttendanceModeEnumeration()::OnlineEventAttendanceMode);
        }

        return $event->setProperty('eventAttendanceMode', Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode);
    }
}
