<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapRelevantOccupation extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        $occupationArea = $data['occupation_area'] ?? null;
        return $jobPosting->relevantOccupation(
            $occupationArea ? [Schema::occupation()->name($occupationArea)] : []
        );
    }
}
