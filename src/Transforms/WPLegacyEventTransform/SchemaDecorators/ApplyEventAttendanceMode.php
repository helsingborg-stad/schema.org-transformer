<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyEventAttendanceMode implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        return $event->setProperty('eventAttendanceMode', Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode);
    }
}
