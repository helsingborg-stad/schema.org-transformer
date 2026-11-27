<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractNorticEventDataMapper implements NorticEventDataMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Event $event, array $data): Event;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    protected function getShows(array $data): array
    {
        return array_values(array_filter($data['shows'] ?? [], 'is_array'));
    }
}
