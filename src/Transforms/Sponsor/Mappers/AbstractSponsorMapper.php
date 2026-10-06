<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use DateTime;

abstract class AbstractSponsorMapper implements SponsorMapperInterface
{
    public function mapDateTime(?string $date, ?string $time): ?DateTime
    {
        return DateTime::createFromFormat(
            'Y-m-d H:i:s',
            trim($date . ' ' . $time)
        ) ?? null;
    }

    public function withAcfFields(?array $data, callable $callback, ?string $subField): mixed
    {
        if (!is_null($data) && !is_null($data['acf'])) {
            return $callback((is_null($subField) || !is_array($data['acf'][$subField])) ? $data['acf'] : $data['acf'][$subField]);
        }
        return null;
    }
}
