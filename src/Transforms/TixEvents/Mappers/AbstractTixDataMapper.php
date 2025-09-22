<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractTixDataMapper implements TixDataMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Event $event, array $data): Event;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    protected function getValidDatesFromSource($data)
    {
        return array_filter(
            $data['Dates'] ?? [],
            fn ($d) => !empty($d['EventId']) && $d['DefaultEventGroupId'] === $data['EventGroupId']
        );
    }

    protected function firstNonEmptyArray(...$values)
    {
        foreach ($values as $value) {
            if (is_array($value) && !empty($value)) {
                return $value;
            }
        }
        return [];
    }
}
