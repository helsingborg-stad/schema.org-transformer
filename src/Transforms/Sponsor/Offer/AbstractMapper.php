<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer;

use DateTime;

abstract class AbstractMapper implements MapperInterface
{
    public function mapDateTime(?string $date, ?string $time): ?DateTime
    {
        return DateTime::createFromFormat(
            'Y-m-d H:i:s',
            trim($date . ' ' . $time)
        ) ?: null;
    }

    public function withAcfFields(?array $data, callable $callback, ?string $subField): mixed
    {
        if (!isset($data['acf']) || !is_array($data['acf'])) {
            return null;
        }

        $acf = $data['acf'];
        if ($subField !== null) {
            if (!isset($acf[$subField]) || !is_array($acf[$subField])) {
                return null;
            }

            $acf = $acf[$subField];
        }

        return $callback($acf);
    }
}
