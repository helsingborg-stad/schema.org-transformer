<?php

namespace SchemaTransformer\Run\Cli;

class Options
{
    /**
     * @param array<string,string|false>|null $options Parsed CLI options; if null, reads from global argv via getopt().
     */
    public function __construct(private readonly ?array $options = null)
    {
    }

    public function getTarget(): Target
    {
        $options = $this->options ?? (getopt('', ['target::']) ?: []);
        $target  = $options['target'] ?? 'console';

        return match ($target) {
            'console'   => Target::Console,
            'typesense' => Target::Typesense,
            default     => throw new \InvalidArgumentException("Invalid target: $target"),
        };
    }
}
