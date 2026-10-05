<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapHiringOrganization extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        $name = $data['organizations'][0]['nameorgunit'] ?? null;
        return $jobPosting->hiringOrganization($name !== null ? [
            Schema::organization()
                        ->name($name)
                        ->ethicsPolicy($data['organizations'][0]['suffix_text'] ?? null)
        ] : []);
    }
}
