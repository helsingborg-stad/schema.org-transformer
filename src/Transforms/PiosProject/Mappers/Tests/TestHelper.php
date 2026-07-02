<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\Assert;
use Municipio\Schema\Schema;
use Municipio\Schema\Project;
use SchemaTransformer\Transforms\PiosProject\Mappers\PiosProjectMapperInterface;

class TestHelper extends Assert
{
    protected function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    public function expectMapperToConvertSourceTo(
        PiosProjectMapperInterface $mapper,
        string $sourceJson,
        Project $expectedProject,
        ?string $message = null
    ): TestHelper {
        $source = $this->prepareJsonForTransform($sourceJson);
        $this->assertNotEmpty($source, 'Source data is empty or invalid JSON');

        $actual = $mapper->map(Schema::project(), $source);

        $this->assertEquals(
            $expectedProject->toArray(),
            $actual->toArray(),
            $message ?: 'Mapper did not produce expected output for given source'
        );

        return $this;
    }
}
