<?php

declare(strict_types=1);

namespace SchemaTransformer\Paginators;

use SchemaTransformer\Interfaces\AbstractPaginator;

final class NullPaginator implements AbstractPaginator
{
    public function getNext(array $headers): string | false
    {
        return false;
    }
}
