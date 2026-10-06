<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\VarbiJobPostingMapperInterface;

abstract class AbstractVarbiJobPostingMapper implements VarbiJobPostingMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(JobPosting $jobPosting, array $data): JobPosting;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    protected function getIncludedAd(array $data): ?array
    {
        return $this->getElementByType($data['included'] ?? [], 'job-ad');
    }

    protected function getElementsByType(array $data, string $type): ?array
    {
        return array_values(array_filter(
            $data ?? [],
            fn($item) => isset($item['type']) && $item['type'] === $type
        )) ?? [];
    }

    protected function getElementByType(array $data, string $type): ?array
    {
        return $this->getElementsByType($data, $type)[0] ?? null;
    }
}
