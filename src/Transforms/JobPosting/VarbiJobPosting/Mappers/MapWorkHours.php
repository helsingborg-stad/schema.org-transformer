<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;

class MapWorkHours extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->workHours(
            $this->getElementByType(
                $this->getIncludedAd($data)['attributes']['texts']['details'] ?? [],
                'working-hours'
            )['text'] ?? null
        );
    }
}
