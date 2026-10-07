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
                    fn($text) => $this->tryCreateTextObject($text),
                    [
                        $this->getIncludedAd($data)['attributes']['texts']['descriptions']['combined'] ?? null,
                        $this->getDetailsTable($data)
                    ]
                )
            ))
        );
    }

    private function getDetailsTable(array $data): string
    {
        return array_reduce(
            $this->getIncludedAd($data)['attributes']['texts']['details'] ?? [],
            fn($carry, $rec) => $carry . (empty($rec['label'] ?? null) || empty($rec['text']) ? '' : '<p><strong>' . htmlentities($rec['label']) . ':</strong> ' . htmlentities($rec['text']) . '</p>'),
            ''
        );
    }

    private function tryCreateTextObject(?string $text)
    {
        return empty($text) ? null : Schema::textObject()->text($text);
    }
}
