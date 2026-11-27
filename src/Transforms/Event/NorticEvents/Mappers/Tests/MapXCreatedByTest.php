<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapXCreatedBy;

#[CoversClass(MapXCreatedBy::class)]
final class MapXCreatedByTest extends TestCase
{
    #[TestDox('event::x-created-by is set to the Nortic transformer identifier')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapXCreatedBy(),
            '{}',
            Schema::event()->setProperty('x-created-by', 'municipio://schema.org-transformer/nortic')
        );
    }
}
