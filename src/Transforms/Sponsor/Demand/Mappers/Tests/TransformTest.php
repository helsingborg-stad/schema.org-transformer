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
            ['guid' => ['rendered' => 'https://example.invalid/?p=42'], 'title' => ['rendered' => 'Demand without ACF']],
        ])[0];

        self::assertSame('https://example.invalid/?p=42', $result['@id']);
        self::assertSame('Demand without ACF', $result['name']);
    }

    public function testMapsWebsiteAndSkipsFalseOptionalFields(): void
    {
        $result = (new Transform())->transform([
            [
                'guid'  => ['rendered' => 'https://example.invalid/?p=42'],
                'title' => ['rendered' => 'Demand'],
                'acf'   => [
                    'organization_website' => 'https://example.invalid',
                    'location'             => false,
                ],
            ],
        ])[0];

        self::assertSame('https://example.invalid', $result['@reverse']['seeks']['url']);
        self::assertArrayNotHasKey('sponsor:supportLocation', $result);
        self::assertSame([], $result['keywords'] ?? []);
    }

    public function testMapsPublicationPage(): void
    {
        $result = (new Transform())->transform([TestHelper::fixture()])[0];
        $page   = $result['mainEntityOfPage'];

        self::assertSame('SponsorDemand', $result['@type']);
        self::assertSame('WebPage', $page['@type']);
        self::assertSame(['@id' => $result['@id']], $page['mainEntity']);
        self::assertSame('2026-09-11T07:46:51Z', $page['datePublished']);
        self::assertSame('2026-11-12T00:00:00+01:00', $page['expires']);
    }
}
