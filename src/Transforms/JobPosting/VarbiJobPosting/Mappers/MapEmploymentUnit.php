<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\JobPosting;

class MapEmploymentUnit extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->employmentUnit(
            array_filter(array_values(
                array_map(
                    fn($item) => empty($item['text']) ? null : Schema::organization()->name($item['text']),
                    $this->getElementsByType(
                        $this->getIncludedAd($data)['attributes']['texts']['details'] ?? [],
                        'organization'
                    ) ?? []
                )
            ))
        );
    }
}
