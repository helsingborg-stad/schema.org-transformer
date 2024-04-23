<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Interfaces\AbstractService;

class Service implements AbstractService
{
    protected AbstractDataReader $reader;
    protected AbstractDataWriter $writer;
    protected AbstractDataTransform $transform;

    public function __construct(AbstractDataReader $reader, AbstractDataWriter $writer, AbstractDataTransform $transform)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->transform = $transform;
    }
    public function execute(string $input, string $output): void
    {
        $data = $this->reader->read($input);
        $result = $this->transform->transform($data);
        $this->writer->write($output, $result);
    }
}
