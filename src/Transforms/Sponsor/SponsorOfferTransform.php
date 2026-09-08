<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;

class SponsorOfferTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }
    public function transform(array $data): array
    {
        return $data;
    }
}
