<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;

class MapRelevantOccupation extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        $taxonomy = $data['data']['relationships']['taxonomy']['data'] ?? null;
        if ($taxonomy) {
            $matchingTaxonomy =
                array_filter(
                    $data['included'] ?? [],
                    fn($item) => $item['id'] === $taxonomy['id'] && $item['type'] === $taxonomy['type']
                )[0] ?? null;
            return $jobPosting->mapRelevantOccupation($matchingTaxonomy['attributes']['name'] ?? null);
        }
        return $jobPosting;
    }
}
