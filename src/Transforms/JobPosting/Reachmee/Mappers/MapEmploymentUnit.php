<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\JobPosting;

class MapEmploymentUnit extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        $unit   = $data['organizations'][1]['nameorgunit'] ?? null;
        $county = $data['areas'][0]['name'] ?? null;
        $city   = $data['areas'][1]['name'] ?? null;
        return $unit !== null ? $jobPosting->employmentUnit(
            Schema::organization()
                        ->name($unit)
                        ->address(
                            Schema::postalAddress()
                                ->addressRegion($county)
                                ->addressLocality($city)
                        )
        )
                        : $jobPosting;
    }
}
