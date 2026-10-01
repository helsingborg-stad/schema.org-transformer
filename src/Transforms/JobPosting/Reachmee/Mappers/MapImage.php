<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;

class MapImage extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->image($data['image_link'] ?? null);
    }
}
