<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\ReachmeeJobPostingMapperInterface;

abstract class AbstractReachmeeJobPostingMapper implements ReachmeeJobPostingMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(JobPosting $jobPosting, array $data): JobPosting;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    protected function pad(?array $array, int $length, array $fallback): array
    {
        return array_pad(empty($array) || !is_array($array) ? [] : $array, $length, $fallback);
    }
}
