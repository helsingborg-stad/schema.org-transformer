<?php

declare(strict_types=1);

namespace SchemaTransformer\IO\V2;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Interfaces\AbstractDataTransform;

#[CoversClass(XmlHttpReader::class)]
final class XmlHttpReaderTest extends TestCase
{
    #[TestDox('decodeBody keeps the XML string under content')]
    public function testDecodeBodyKeepsXmlContent(): void
    {
        $reader = new XmlHttpReader('', $this->createMock(AbstractDataTransform::class));
        $decode = new \ReflectionMethod($reader, 'decodeBody');

        $this->assertSame(
            ['content' => '<Assignments></Assignments>'],
            $decode->invoke($reader, '<Assignments></Assignments>')
        );
    }
}
