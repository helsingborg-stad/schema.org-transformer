<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\JobPosting\Reachmee\ReachmeeJobPostingTransform;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('jobPosting::identifier is formatted from project_id')]
    public function testFormatsProjectIdAsIdentifier(): void
    {
        $jobPosting = Schema::jobPosting()->setProperty('project_id', '12345');
        $mapper     = new MapIdentifier(new ReachmeeJobPostingTransform('reachmee-'));

        $actual = $mapper->map($jobPosting, ['project_id' => 'ignored']);

        $expected = Schema::jobPosting()
            ->setProperty('project_id', '12345')
            ->identifier('reachmee-12345');

        $this->assertEquals($expected->toArray(), $actual->toArray());
    }
}
