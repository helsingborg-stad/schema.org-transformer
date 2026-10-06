<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapDescription extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {

        return $jobPosting->description(
            array_values(array_filter(
                array_map(
                    fn($text) => empty($text) ? null : Schema::textObject()->text($text),
                    [$this->getIncludedAd($data)['attributes']['texts']['descriptions']['combined'] ?? null]
                )
            ))
        );
    }
}
