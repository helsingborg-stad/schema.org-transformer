<?php

namespace SchemaTransformer\Run\Cli;

class Options implements OptionsInterface
{
    public function getTarget(): Target
    {
        $options = getopt('', ['target::']);
        $target  = $options['target'] ?? 'console';

        return match ($target) {
            'console'   => Target::Console,
            'typesense' => Target::Typesense,
            default     => throw new \InvalidArgumentException("Invalid target: $target"),
        };
    }
}
