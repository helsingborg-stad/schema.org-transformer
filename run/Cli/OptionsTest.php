<?php

namespace SchemaTransformer\Run\Cli;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    #[TestDox('returns console target by default')]
    public function testReturnsConsoleTargetByDefault(): void
    {
        $options = new Options([]);

        $this->assertSame(Target::Console, $options->getTarget());
    }

    #[TestDox('returns console target when target is console')]
    public function testReturnsConsoleTargetWhenTargetIsConsole(): void
    {
        $options = new Options(['target' => 'console']);

        $this->assertSame(Target::Console, $options->getTarget());
    }

    #[TestDox('returns typesense target when target is typesense')]
    public function testReturnsTypesenseTargetWhenTargetIsTypesense(): void
    {
        $options = new Options(['target' => 'typesense']);

        $this->assertSame(Target::Typesense, $options->getTarget());
    }

    #[TestDox('throws InvalidArgumentException for an invalid target')]
    public function testThrowsInvalidArgumentExceptionForInvalidTarget(): void
    {
        $options = new Options(['target' => 'invalid']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid target: invalid');

        $options->getTarget();
    }
}
