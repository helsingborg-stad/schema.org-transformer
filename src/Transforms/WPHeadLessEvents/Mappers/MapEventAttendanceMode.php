<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapEventAttendanceMode extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->eventAttendanceMode(
                    match ($data['acf']['attendancemode'] ?? '') {
                        'online' => Schema::eventAttendanceModeEnumeration()::OnlineEventAttendanceMode,
                        'offline' => Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode,
                        'mixed' => Schema::eventAttendanceModeEnumeration()::MixedEventAttendanceMode,
                        default => Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode,
                    }
                );
    }
}
