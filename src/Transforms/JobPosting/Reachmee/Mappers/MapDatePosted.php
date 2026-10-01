<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;

class MapDatePosted extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->datePosted($data['publishing_date'] ?? null);
    }
}
