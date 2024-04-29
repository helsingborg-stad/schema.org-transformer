<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractDataConverter;
use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Interfaces\AbstractService;

class Service implements AbstractService
{
    protected AbstractDataReader $reader;
    protected AbstractDataWriter $writer;
    protected AbstractDataTransform $transform;
    protected AbstractDataConverter $converter;
    protected array $config = [];

    public function __construct(AbstractDataReader $reader, AbstractDataWriter $writer, AbstractDataTransform $transform, AbstractDataConverter $converter, array $config)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->transform = $transform;
        $this->converter = $converter;
        $this->config = $config;
    }
    public function execute(string $source, string $destination): bool
    {
        $data = $this->reader->read($source, $this->config);
        if (false === $data) {
            return false;
        }
        $result = $this->converter->encode($this->transform->transform($data));

        if (false === $this->writer->write($destination, $result, $this->config)) {
            return false;
        }
        return true;
    }
}
