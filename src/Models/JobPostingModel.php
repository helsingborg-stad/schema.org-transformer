<?php

declare(strict_types=1);

namespace SchemaTransformer\Models;

use SchemaTransformer\Interfaces\AbstractModel;

class JobPostingModel implements AbstractModel
{
    public function transformData(array $data): array
    {
        $ld = [
            "@context" => "http://schema.org",
            "@type" => "JobPosting",
            "identifier" => $data["ad_id"],
            "title" => $data["title"],
            "description" => $data["description"],
            "datePosted" => $data["publishing_date"],
            "validThrough" => $data["expiration_date"],
            "employmentType" => "Temporary"
        ];
        return $ld;
    }
}
