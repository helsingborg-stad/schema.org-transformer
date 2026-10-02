<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\ReachmeeJobPostingTransform;
use Spatie\Snapshots\MatchesSnapshots;

#[CoversClass(ReachmeeJobPostingTransform::class)]
final class ReachmeeJobPostingTransformTest extends TestCase
{
    use MatchesSnapshots;

    protected array $data;

    protected function setUp(): void
    {
        $this->data = [
            [
                "ad_id"                     => 1,
                "project_id"                => 2,
                "publishing_date"           =>  "2024-04-01",
                "expiration_date"           => "2024-05-01",
                "title"                     => "title",
                "description"               => "description",
                "hide_apply_button"         => false,
                "working_hours"             => null,
                "working_hours_id"          => null,
                "country_code"              => 143,
                "country"                   => "Sverige",
                "position"                  => "position",
                "position_id"               => 215,
                "occupation_area_id"        => 56,
                "occupation_area"           => "occupation_area",
                "occupation_orientation_id" => null,
                "occupation_orientation"    => null,
                "occupation_degree_id"      => 2,
                "occupation_degree"         => "occupation_degree",
                "employment_level"          => "employment_level",
                "employment_level_id"       => 3,
                "ad_language"               => "SE",
                "image_link"                => null,
                "image_alt_text"            => null,
                "prefix_text"               => "prefix_text",
                "suffix_text"               => "suffix_text",
                "link"                      => "https://",
                "url"                       => "&r",
                "contact_persons"           => [
                    [
                        "first_name" => "first_name_1",
                        "surname"    => "surname_1",
                        "email"      => "email_1",
                        "phone"      => "phone_1",
                        "position"   => "position_1"
                    ],
                    [
                        "first_name" => "first_name_2",
                        "surname"    => "surname_2",
                        "email"      => "email_2",
                        "phone"      => "phone_2",
                        "position"   => "position_2"
                    ]
                ],
                "organizations"             => [
                    [
                        "orgunitseqno" => 1,
                        "nameorgunit"  => "nameorgunit_1"
                    ],
                    [
                        "orgunitseqno" => 2,
                        "nameorgunit"  => "nameorgunit_2"
                    ],
                ],
                "areas"                     => [
                    [
                        "id"   => 1,
                        "name" => "name_1"
                    ],
                    [
                        "id"   => 2,
                        "name" => "name_2"
                    ]
                ]
            ]
        ];
    }
    public function testJobPostingTransform(): void
    {
        $model = new ReachmeeJobPostingTransform([], "");
        $this->assertMatchesJsonSnapshot(json_encode($model->transform($this->data), JSON_PRETTY_PRINT));
    }

    public function testRequiresAdId()
    {
        $data  = [[]];
        $model = new ReachmeeJobPostingTransform([], "");

        $this->assertEmpty($model->transform($data));
    }

    public function testEmptyDataReturnsEmptyArray()
    {
        $data  = [];
        $model = new ReachmeeJobPostingTransform([], "");

        $this->assertEmpty($model->transform($data));
    }

    public function testSanitizertsAreApplied()
    {
        $data  = [['project_id' => 123, 'title' => 'original']];
        $model = new ReachmeeJobPostingTransform([
            new class {
                public function sanitize(array $data): array
                {
                    $data['title'] = 'sanitized';
                    return $data;
                }
            }
        ], "");

        $this->assertEquals('sanitized', $model->transform($data)[0]['title']);
    }
}
