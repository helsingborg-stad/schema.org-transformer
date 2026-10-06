<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapHiringOrganization extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->hiringOrganization(
            array_map(
                fn($item) => empty($item['text']) ? null : Schema::organization()->name($item['text']),
                $this->getElementsByType(
                    $this->getIncludedAd($data)['attributes']['texts']['details'] ?? [],
                    'organization'
                ) ?? []
            )
        );
    }
}
