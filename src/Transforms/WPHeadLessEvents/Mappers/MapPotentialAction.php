<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapPotentialAction extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->potentialAction(
            array_values(
                array_filter(
                    [
                        $data['acf']['onlineAttendenceUrl'] ?? null
                        ? Schema::action()
                            ->type('JoinAction')
                            ->description('Delta i onlineevent')
                            ->url($data['acf']['onlineAttendenceUrl'])
                        : null
                    ]
                )
            )
        );
    }
}
