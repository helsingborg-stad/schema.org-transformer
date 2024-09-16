<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class ReachmeeJobPostingTransform implements AbstractDataTransform
{
    /**
     * @param \SchemaTransformer\Interfaces\SanitizerInterface[] $sanitizers
     */
    public function __construct(private array $sanitizers)
    {
    }

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

        if (empty($data)) {
            return [];
        }

        foreach ($data as &$row) {
            foreach ($this->sanitizers as $sanitizer) {
                $row = $sanitizer->sanitize($row);
            }

            [$county, $city] = $this->normalizeArray($row['areas'] ?? [], 2, ["name" => ""]);
            [$name, $unit]   = $this->normalizeArray($row['organizations'] ?? [], 2, ["nameorgunit" => ""]);

            if (empty($row['ad_id'])) {
                continue;
            }

            $directAppply = isset($row['hide_apply_button']) && !$row['hide_apply_button'] ? true : false;

            $jobPosting = Schema::jobPosting()
                ->identifier((string) $row['ad_id'])
                ->title($row['title'] ?? null)
                ->employerOverview($row['prefix_text'] ?? null)
                ->description($row['description'] ?? null)
                ->datePosted($row['publishing_date'] ?? null)
                ->validThrough($row['expiration_date'] ?? null)
                ->employmentType($row['occupation_degree'] ?? null)
                ->image($row['image_link'] ?? null)
                ->url($row['link'] ?? '')
                ->directApply($directAppply)
                ->workHours($row['working_hours'] ?? null)
                ->relevantOccupation(
                    Schema::occupation()
                        ->name($row['occupation_area'] ?? null)
                )->hiringOrganization(
                    Schema::organization()
                        ->name($name['nameorgunit'])
                        ->ethicsPolicy($row['suffix_text'] ?? null)
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
            $jobPosting->setProperty('@version', md5(json_encode($jobPosting->toArray())));
            $output[] = $jobPosting->toArray();
        }
        return $output;
    }
}
