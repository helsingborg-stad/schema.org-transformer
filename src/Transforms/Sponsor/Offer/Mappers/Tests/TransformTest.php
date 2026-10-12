<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Transform;

#[CoversClass(Transform::class)]
final class TransformTest extends TestCase
{
    public function testPreservesTopLevelFieldsWhenAcfIsMissing(): void
    {
        $result = (new Transform())->transform([
            ['id' => 42, 'title' => ['rendered' => 'Offer without ACF']],
        ])[0];

        self::assertSame(42, $result['@id']);
        self::assertSame('Offer without ACF', $result['name']);
    }

    public function testMapsWebsiteAndAllowsMissingResources(): void
    {
        $result = (new Transform())->transform([
            [
                'id'    => 42,
                'title' => ['rendered' => 'Offer'],
                'acf'   => [
                    'organization_website' => 'https://example.invalid',
                    'resources'            => false,
                    'activities'           => false,
                ],
            ],
        ])[0];

        self::assertSame('https://example.invalid', $result['offeredBy']['url']);
        self::assertSame([], $result['category'] ?? []);
    }
}
