<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class JobPostTransform implements AbstractDataTransform
{
    public function transform(array $data): array
    {
        $output = [];
        foreach ($data as &$row) {
            $jobPosting = Schema::jobPosting()
                ->identifier($row['ad_id'])
                ->title($row['title'])
                ->description($row['description'])
                ->datePosted($row['publishing_date'])
                ->validThrough($row['expiration_date'])
                ->employmentType($row['occupation_degree'])
                ->image($row['image_link'])
                ->url($row['link']);

            if (is_array($row['contact_persons'])) {
                $contacts = [];
                foreach ($row['contact_persons'] as &$contact) {
                    $contacts[] =                     Schema::contactPoint()
                        ->contactType($contact['position'])
                        ->name($contact['first_name'] . ' ' . $contact['surname'])
                        ->email($contact['email'])
                        ->telephone($contact['phone']);
                }
                $jobPosting->applicationContact($contacts);
            }
        }
        $output[] = $jobPosting->toArray();
        return $output;
    }
}
