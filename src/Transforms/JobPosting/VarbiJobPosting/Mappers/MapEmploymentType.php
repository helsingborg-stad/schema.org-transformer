<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;

class MapEmploymentType extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        // TODO: Add emplyment-hours, working-hours?
        return $jobPosting->employmentType(
            $this->getElementByType(
                $this->getIncludedAd($data)['attributes']['texts']['details'] ?? [],
                'employment-type'
            )['text'] ?? null
        );
    }
}
