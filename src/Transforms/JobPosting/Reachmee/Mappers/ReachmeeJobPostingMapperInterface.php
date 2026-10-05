<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;

interface ReachmeeJobPostingMapperInterface
{
    public function map(JobPosting $jobPosting, array $data): JobPosting;
}
