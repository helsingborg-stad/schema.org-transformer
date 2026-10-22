<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\Assert;
use Municipio\Schema\Schema;
use Municipio\Schema\JobPosting;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\VarbiJobPostingMapperInterface;

class TestHelper extends Assert
{
    protected function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    public function expectMapperToConvertSourceTo(
        VarbiJobPostingMapperInterface $mapper,
        string $sourceJson,
        JobPosting $expectedEvent,
        ?string $message = null
    ): TestHelper {
        $source = $this->prepareJsonForTransform($sourceJson);
        $this->assertNotEmpty($source, 'Source data is empty or invalid JSON');

        $actual = $mapper->map(Schema::jobPosting(), $source);

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actual->toArray(),
            $message ?: 'Mapper did not produce expected output for given source'
        );

        return $this;
    }
}
