<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;

class MapDescription extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->description(
            array_values(array_filter([$this->tryCreateTextObject($data['description'] ?? null)]))
        );
    }

    public function tryCreateTextObject(?string $text): ?TextObject
    {
        return $text ? Schema::textObject()->text($text) : null;
    }
}
