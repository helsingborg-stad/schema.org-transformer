<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractWPLegacyEventMapper implements WPLegacyEventMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Event $event, array $data): Event;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }
}
