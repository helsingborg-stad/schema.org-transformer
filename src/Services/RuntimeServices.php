<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractDataConverter;
use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Interfaces\AbstractService;
use SchemaTransformer\Transforms\ReachmeeJobPostingTransform;
use SchemaTransformer\Transforms\StratsysTransform;

class RuntimeServices
{
    private AbstractService $jobPostingService;
    private AbstractService $stratsysService;

    public function __construct(AbstractDataReader $reader, AbstractDataWriter $writer, AbstractDataConverter $converter)
    {
        $this->jobPostingService = new Service(
            $reader,
            $writer,
            new ReachmeeJobPostingTransform(),
            $converter
        );
        $this->stratsysService = new Service(
            $reader,
            $writer,
            new StratsysTransform(),
            $converter
        );
    }
    public function getJobPostingService(): AbstractService
    {
        return $this->jobPostingService;
    }
    public function getStratsysService(): AbstractService
    {
        return $this->stratsysService;
    }
}
