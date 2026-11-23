<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Assert;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\NorticEventDataMapperInterface;

class TestHelper extends Assert
{
    protected function prepareJsonForTransform(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    public function expectMapperToConvertSourceTo(
        NorticEventDataMapperInterface $mapper,
        string $sourceJson,
        Event $expectedEvent,
        ?string $message = null
    ): TestHelper {
        $source = $this->prepareJsonForTransform($sourceJson);
        $this->assertNotSame('', trim($sourceJson), 'Source JSON is empty');

        $actual = $mapper->map(Schema::event(), $source);

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actual->toArray(),
            $message ?: 'Mapper did not produce expected output for given source'
        );

        return $this;
    }
}
