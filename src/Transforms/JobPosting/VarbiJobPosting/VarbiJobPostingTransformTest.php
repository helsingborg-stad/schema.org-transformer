<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(VarbiJobPostingTransform::class)]
final class VarbiJobPostingTransformTest extends TestCase
{
    #[TestDox('transforms a complete Varbi job posting')]
    public function testTransformsCompleteJobPosting(): void
    {
        $transform = new VarbiJobPostingTransform('varbi-');

        $input = [
            [
                'data'     => [
                    'id'            => '987',
                    'attributes'    => [
                        'dates'        => [
                            'published' => '2026-09-15',
                            'deadline'  => '2026-10-30',
                        ],
                        'translations' => [
                            'texts' => [
                                'title' => 'Systemutvecklare',
                            ],
                        ],
                    ],
                    'links'         => [
                        'apply'       => 'https://example.com/apply/987',
                        'ad_rendered' => 'https://example.com/jobs/987',
                    ],
                    'relationships' => [
                        'taxonomy' => [
                            'data' => [
                                'id'   => 'taxonomy-42',
                                'type' => 'taxonomy',
                            ],
                        ],
                    ],
                ],
                'included' => [
                    [
                        'id'         => 'job-ad-987',
                        'type'       => 'job-ad',
                        'attributes' => [
                            'contacts'       => [
                                [
                                    'title' => 'Rekryterare',
                                    'name'  => 'Anna Andersson',
                                    'email' => 'anna@example.com',
                                    'phone' => '+46 40 123 456',
                                ],
                            ],
                            'union_contacts' => [
                                [
                                    'name'  => 'Facklig kontakt',
                                    'email' => 'union@example.com',
                                    'phone' => '+46 40 555 010',
                                ],
                            ],
                            'texts'          => [
                                'descriptions' => [
                                    'combined' => 'Arbeta med utveckling.',
                                ],
                                'details'      => [
                                    [
                                        'type' => 'employment-type',
                                        'text' => 'Heltid',
                                    ],
                                    [
                                        'type' => 'working-hours',
                                        'text' => '40 timmar per vecka',
                                    ],
                                    [
                                        'type' => 'organization',
                                        'text' => 'Tekniska förvaltningen',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id'         => 'taxonomy-42',
                        'type'       => 'taxonomy',
                        'attributes' => [
                            'name' => 'IT och data',
                        ],
                    ],
                ],
            ],
        ];

        $expected = Schema::jobPosting()
            ->applicationContact([
                Schema::contactPoint()
                    ->contactType('Rekryterare')
                    ->name('Anna Andersson')
                    ->email('anna@example.com')
                    ->telephone('+46 40 123 456'),
                Schema::contactPoint()
                    ->contactType('Facklig företrädare')
                    ->name('Facklig kontakt')
                    ->email('union@example.com')
                    ->telephone('+46 40 555 010'),
            ])
            ->datePosted('2026-09-15')
            ->description([
                Schema::textObject()->text('Arbeta med utveckling.'),
            ])
            ->directApply(true)
            ->employmentType('Heltid')
            ->employmentUnit([
                Schema::organization()->name('Tekniska förvaltningen'),
            ])
            ->hiringOrganization([
                Schema::organization()->name('Tekniska förvaltningen'),
            ])
            ->identifier('varbi-987')
            ->image([])
            ->mapRelevantOccupation('IT och data')
            ->title('Systemutvecklare')
            ->url('https://example.com/jobs/987')
            ->validThrough('2026-10-30')
            ->workHours('40 timmar per vecka')
            ->setProperty('x-created-by', 'municipio://schema.org-transformer/varbi-jobposting');

        $this->assertEquals([$expected->toArray()], $transform->transform($input));
    }

    #[TestDox('transforms multiple Varbi job postings')]
    public function testTransformsMultipleJobPostings(): void
    {
        $transform = new VarbiJobPostingTransform('varbi-');
        $input     = [
            ['data' => ['id' => '101']],
            ['data' => ['id' => '202']],
        ];

        $result = $transform->transform($input);

        $this->assertSame(['varbi-101', 'varbi-202'], array_column($result, '@id'));
    }

    #[TestDox('returns an empty array for empty input')]
    public function testEmptyDataReturnsEmptyArray(): void
    {
        $transform = new VarbiJobPostingTransform('varbi-');

        $this->assertSame([], $transform->transform([]));
    }
}
