<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Transform;

#[CoversClass(Transform::class)]
final class TransformTest extends TestCase
{
    public function testPreservesTopLevelFieldsWhenAcfIsMissing(): void
    {
        $result = (new Transform())->transform([
            ['id' => 42, 'title' => ['rendered' => 'Demand without ACF']],
        ])[0];

        self::assertSame(42, $result['@id']);
        self::assertSame('Demand without ACF', $result['name']);
    }

    public function testMapsWebsiteAndSkipsFalseOptionalFields(): void
    {
        $result = (new Transform())->transform([
            [
                'id'    => 42,
                'title' => ['rendered' => 'Demand'],
                'acf'   => [
                    'organization_website'             => 'https://example.invalid',
                    'location'                         => false,
                    'organization_eligible_for_grants' => false,
                ],
            ],
        ])[0];

        self::assertSame('https://example.invalid', $result['organization']['url']);
        self::assertArrayNotHasKey('location', $result);
        self::assertSame([], $result['keywords'] ?? []);
    }

    public function testMapsTrueGrantEligibilityAsText(): void
    {
        $result = (new Transform())->transform([
            [
                'acf' => ['organization_eligible_for_grants' => true],
            ],
        ])[0];

        self::assertSame('eligible', $result['keywords'][0]['name']);
    }
}
