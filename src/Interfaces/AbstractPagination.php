<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractPaginator
{
    public function getNext(array $headers): string | false;
}
