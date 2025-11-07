<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\Assert;
use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\ElementarySchoolDataMapperInterface;

class TestHelper extends Assert
{
    protected function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    public function expectMapperToConvertSourceTo(
        ElementarySchoolDataMapperInterface $mapper,
        string $sourceJson,
        ElementarySchool $expectedSchool,
        string $message = null
    ): TestHelper {
        $source = $this->prepareJsonForTransform($sourceJson);
        $this->assertNotEmpty($source, 'Source data is empty or invalid JSON');

        $actual = $mapper->map(Schema::elementarySchool(), $source);

        $this->assertEquals(
            $expectedSchool->toArray(),
            $actual->toArray(),
            $message ?: 'Mapper did not produce expected output for given source'
        );

        return $this;
    }
}
