<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Encoders\JSONLConverter;
use SchemaTransformer\Interfaces\AbstractDataConverter;
use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Interfaces\AbstractService;
use SchemaTransformer\Transforms\JobPostingTransform;

class RuntimeServices
{
    private AbstractService $jobPostingService;

    public function __construct(AbstractDataReader $reader, AbstractDataWriter $writer, AbstractDataConverter $converter)
    {
        $this->jobPostingService = new Service(
            $reader,
            $writer,
            new JobPostingTransform(),
            $converter,
            []
        );
    }
    public function getJobPostingService(): AbstractService
    {
        return $this->jobPostingService;
    }
}
