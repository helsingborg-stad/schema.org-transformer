<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ImageObject;
use Municipio\Schema\JobPosting;

class MapImage extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->image(
            array_values(
                array_filter(
                    array_map(
                        fn($item) => $item
                            ? $this->tryCreateImageObject(
                                $item['top_image'] ?? null,
                                $item['top_image_alt_text'] ?? null
                            )
                            : null,
                        array_filter([$this->getIncludedAd($data)['attributes']['media'] ?? null])
                    ),
                )
            )
        );
    }

    private function tryCreateImageObject(?string $url, ?string $alt): ?ImageObject
    {
        return $url
            ? Schema::imageObject()
                ->url($url)
                ->name($alt)
                ->caption($alt)
            : null;
    }
}
