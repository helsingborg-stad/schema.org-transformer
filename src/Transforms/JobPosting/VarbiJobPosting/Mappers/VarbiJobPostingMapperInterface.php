<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;

interface VarbiJobPostingMapperInterface
{
    public function map(JobPosting $jobPosting, array $data): JobPosting;
}
