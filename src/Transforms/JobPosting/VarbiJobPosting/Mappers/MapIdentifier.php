<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\JobPosting;

class MapIdentifier extends AbstractVarbiJobPostingMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->identifier($this->formatId((string)$data['data']['id'] ?? ""));
    }
}
