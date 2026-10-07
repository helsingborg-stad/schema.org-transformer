<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;

class MapDatePosted extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->datePosted(
            $data['data']['attributes']['dates']['published'] ?? null
        );
    }
}
