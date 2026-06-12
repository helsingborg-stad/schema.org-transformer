<?php

declare(strict_types=1);

namespace SchemaTransformer\Util;

use DateTime;
use DateTimeZone;

class DateUtils
{
    public static function toLocalDate($date, $sourceTimezone = 'UTC', $targetTimezone = 'Europe/Stockholm'): ?string
    {
        if (empty($date)) {
            return null;
        }

        $datetime = new DateTime($date, new DateTimeZone($sourceTimezone));
        $datetime->setTimezone(new DateTimeZone($targetTimezone));
        // return $datetime->format("Y-m-d\TH:i:sP");
        return $datetime->format("Y-m-d\TH:i:s");
    }
}
