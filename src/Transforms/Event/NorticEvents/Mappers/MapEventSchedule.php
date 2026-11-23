<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapEventSchedule extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $schedules = array_map(
            fn (array $show) => Schema::schedule()
                ->startDate($show['startDate'] ?? null)
                ->endDate($this->getShowEndDate($show))
                ->url($show['link'] ?? null),
            $this->getShows($data)
        );

        return $event->eventSchedule($schedules);
    }

    private function getShowEndDate(array $show): ?string
    {
        $startDate = $show['startDate'] ?? null;
        $duration  = $show['playTimeInMinutes'] ?? null;

        if (!is_string($startDate) || !is_numeric($duration) || (int) $duration <= 0) {
            return null;
        }

        $start = \DateTimeImmutable::createFromFormat('!Y-m-d H:i', $startDate);
        if ($start === false) {
            return null;
        }

        return $start->modify('+' . (int) $duration . ' minutes')->format('Y-m-d H:i');
    }
}
