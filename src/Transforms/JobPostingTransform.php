<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class JobPostingTransform implements AbstractDataTransform
{
    protected function normalizeArray(?array $in, int $length, array $fallback): array
    {
        $array = $in;
        if (empty($in) || !is_array($in)) {
            $array = [];
        }
        return array_pad($array, $length, $fallback);
    }

    public function transform(array $data): array
    {
        $output = [];
        foreach ($data as &$row) {
            [$county, $city] = $this->normalizeArray($row['areas'], 2, ["name" => ""]);
            [$name, $unit] = $this->normalizeArray($row['organizations'], 2, ["nameorgunit" => ""]);

            $jobPosting = Schema::jobPosting()
                ->identifier((string) $row['ad_id'])
                ->title($row['title'])
                ->employerOverview($row['prefix_text'])
                ->description($row['description'])
                ->datePosted($row['publishing_date'])
                ->validThrough($row['expiration_date'])
                ->employmentType($row['occupation_degree'])
                ->image($row['image_link'])
                ->url($row['link'])
                ->directApply(!$row['hide_apply_button'])
                ->workHours($row['working_hours'])
                ->relevantOccupation(
                    Schema::occupation()
                        ->name($row['occupation_area'])
                )->hiringOrganization(
                    Schema::organization()
                        ->name($name['nameorgunit'])
                        ->ethicsPolicy($row['suffix_text'])
                )->employmentUnit(
                    Schema::organization()
                        ->name($unit['nameorgunit'])
                        ->address(
                            Schema::postalAddress()
                                ->addressRegion($county['name'])
                                ->addressLocality($city['name'])
                        )
                );

            if (!empty($row['contact_persons']) && is_array($row['contact_persons'])) {
                $contacts = [];
                foreach ($row['contact_persons'] as &$contact) {
                    $contacts[] = Schema::contactPoint()
                        ->contactType($contact['position'])
                        ->name($contact['first_name'] . ' ' . $contact['surname'])
                        ->email($contact['email'])
                        ->telephone($contact['phone']);
                }
                $jobPosting->applicationContact($contacts);
            }
            $output[] = $jobPosting->toArray();
        }
        return $output;
    }
}
