<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

interface SponsorMapperInterface
{
    public function map(array $data): mixed;
}
