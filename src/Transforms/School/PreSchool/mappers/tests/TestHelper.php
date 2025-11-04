<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\Assert;
use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\PreSchoolDataMapperInterface;

class TestHelper extends Assert
{
    protected function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    public function expectMapperToConvertSourceTo(
        PreSchoolDataMapperInterface $mapper,
        string $sourceJson,
        Preschool $expectedSchool,
        string $message = null
    ): TestHelper {
        $source = $this->prepareJsonForTransform($sourceJson);
        $this->assertNotEmpty($source, 'Source data is empty or invalid JSON');

        $actual = $mapper->map(Schema::preschool(), $source);

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actual->toArray(),
            $message ?: 'Mapper did not produce expected output for given source'
        );

        return $this;
    }
}
