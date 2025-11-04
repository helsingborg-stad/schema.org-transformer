<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Interfaces\AbstractDataConverter;
use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Interfaces\AbstractService;
use SchemaTransformer\Transforms\DataSanitizers\SanitizeReachmeeJobPostingLink;
use SchemaTransformer\Transforms\ReachmeeJobPostingTransform;
use SchemaTransformer\Transforms\StratsysTransform;
use SchemaTransformer\Transforms\WPExhibitionEventTransform;
use SchemaTransformer\Transforms\School\ElementarySchoolTransform;
use SchemaTransformer\Transforms\School\PreSchool\PreSchoolTransform;
use SchemaTransformer\Transforms\TixEvents\TixEventTransform;
use SchemaTransformer\Transforms\WPLegacyEvents\WPLegacyEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;

class RuntimeServices
{
    private AbstractService $jobPostingService;
    private AbstractService $stratsysService;
    private AbstractService $wpLegacyEventService;
    private AbstractService $wpEventService;
    private AbstractService $wpExhibitionEventService;
    private AbstractService $elementarySchoolService;
    private AbstractService $preSchoolService;
    private AbstractService $tixEventService;

    public function __construct(
        AbstractDataReader $reader,
        AbstractDataWriter $writer,
        AbstractDataConverter $converter,
        string $idprefix,
        private ?TypesenseClient $typesenseClient = null
    ) {
        $reachmeeJobPostingSanitizers = [
        new SanitizeReachmeeJobPostingLink()
        ];

        $this->jobPostingService        = new Service(
            $reader,
            $writer,
            new ReachmeeJobPostingTransform($reachmeeJobPostingSanitizers, $idprefix),
            $converter
        );
        $this->stratsysService          = new Service(
            $reader,
            $writer,
            new StratsysTransform($idprefix),
            $converter
        );
        $this->wpLegacyEventService     = new Service(
            $reader,
            $writer,
            new WPLegacyEventTransform($idprefix),
            $converter
        );
        $this->wpEventService           = new Service($reader, $writer, new WPHeadlessEventTransform($idprefix), $converter);
        $this->wpExhibitionEventService = new Service($reader, $writer, new WPExhibitionEventTransform(), $converter);
        $this->elementarySchoolService  = new Service($reader, $writer, new ElementarySchoolTransform($this->typesenseClient), $converter);
        $this->preSchoolService         = new Service($reader, $writer, new PreSchoolTransform($idprefix, $this->typesenseClient), $converter);
        $this->tixEventService          = new Service($reader, $writer, new TixEventTransform($idprefix), $converter);
    }

    public function getJobPostingService(): AbstractService
    {
        return $this->jobPostingService;
    }
    public function getStratsysService(): AbstractService
    {
        return $this->stratsysService;
    }
    public function getWPLegacyEventService(): AbstractService
    {
        return $this->wpLegacyEventService;
    }
    public function getWPEventService(): AbstractService
    {
        return $this->wpEventService;
    }
    public function getWPExhibitionEventService(): AbstractService
    {
        return $this->wpExhibitionEventService;
    }
    public function getElementarySchoolService(): AbstractService
    {
        return $this->elementarySchoolService;
    }
    public function getPreSchoolService(): AbstractService
    {
        return $this->preSchoolService;
    }

    public function getTixService(): AbstractService
    {
        return $this->tixEventService;
    }
}
