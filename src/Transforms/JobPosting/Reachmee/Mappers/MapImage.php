<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;
use Municipio\Schema\ImageObject;

class MapImage extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->image(
            array_values(array_filter([
                $this->tryCreateImageObject($data['image_link'] ?? null, $data['image_alt_text'] ?? null)
            ]))
        );
    }

    public function tryCreateImageObject(?string $imageLink, ?string $imageAltText): ?ImageObject
    {
        if ($imageLink === null) {
            return null;
        }
        return Schema::imageObject()
            ->url($imageLink)
            ->caption($imageAltText)
            ->description($imageAltText);
    }
}
