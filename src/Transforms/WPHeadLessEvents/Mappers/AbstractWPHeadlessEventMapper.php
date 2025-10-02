<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\WPHeadlessEventMapperInterface;

abstract class AbstractWPHeadlessEventMapper implements WPHeadlessEventMapperInterface
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
