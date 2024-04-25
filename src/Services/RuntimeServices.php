<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Interfaces\AbstractService;
use SchemaTransformer\Transforms\JobPostingTransform;

class RuntimeServices
{
    private AbstractService $jobPostingService;

    public function __construct(AbstractDataReader $reader, AbstractDataWriter $writer)
    {
        $this->jobPostingService = new Service(
            $reader,
            $writer,
            new JobPostingTransform()
        );
    }
    public function getJobPostingService(): AbstractService
    {
        return $this->jobPostingService;
    }
}
